<?php

declare(strict_types=1);

namespace Air\Crud\Controller;

use Air\Core\Exception\ClassWasNotFound;
use Air\Crud\Locale;
use Air\Form\Element\Checkbox;
use Air\Form\Exception\FilterClassWasNotFound;
use Air\Form\Exception\ValidatorClassWasNotFound;
use Air\Form\Form;
use Air\Model\Exception\CallUndefinedMethod;
use Air\Model\Exception\ConfigWasNotProvided;
use Air\Model\Exception\DriverClassDoesNotExists;
use Air\Model\Exception\DriverClassDoesNotExtendsFromDriverAbstract;
use Exception;
use Air\Core\Exception\DomainMustBeProvided;
use Air\Form\Element\Text;

/**
 * @mod-manageable true
 */
class Admin extends Multiple
{
  /**
   * @return array[]
   * @throws ClassWasNotFound
   */
  protected function getHeader(): array
  {
    return [
      'name' => ['title' => Locale::t('Name'), 'by' => 'name'],
      'login' => ['title' => Locale::t('Login'), 'by' => 'login'],
      'enabled' => ['title' => Locale::t('Activity'), 'by' => 'enabled', 'type' => 'bool'],
    ];
  }

  /**
   * @return string
   * @throws ClassWasNotFound
   */
  protected function getTitle(): string
  {
    return Locale::t('Users');
  }

  /**
   * @return string[]
   */
  protected function getAdminMenuItem(): array
  {
    return ['icon' => 'users'];
  }

  /**
   * @return string
   */
  public function getModelClassName(): string
  {
    return \Air\Crud\Model\Admin::class;
  }

  /**
   * @param $model
   * @return Form
   * @throws ClassWasNotFound
   */
  public function getForm($model = null): Form
  {
    return new Form(['data' => $model], [
      'Credentials' => [
        new Checkbox('enabled', [
          'label' => Locale::t('Enabled'),
        ]),
        new Text('name', [
          'label' => Locale::t('Name'),
        ]),
        new Text('login', [
          'label' => Locale::t('Login'),
        ]),
        new Text('new-password', [
          'value' => '',
          'label' => Locale::t('Password'),
          'allowNull' => true,
        ]),
      ]
    ]);
  }

  /**
   * @param string|null $id
   * @return array
   * @throws ClassWasNotFound
   * @throws DomainMustBeProvided
   * @throws FilterClassWasNotFound
   * @throws ValidatorClassWasNotFound
   * @throws CallUndefinedMethod
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   * @throws Exception
   */
  public function manage(string $id = null)
  {
    $model = \Air\Crud\Model\Admin::fetchObject([
      'id' => $this->getRequest()->getParam('id')
    ]);

    $form = $this->getForm($model);

    if ($this->getRequest()->isPost()) {
      if ($form->isValid($this->getRequest()->getPostAll())) {

        $formData = $form->getValues();

        if (strlen($formData['new-password'])) {
          $formData['password'] = md5($formData['new-password']);
        }
        unset($formData['new-password']);

        $model->populate($formData);
        $model->save();

        return ['url' => $this->getRouter()->assemble(
          ['controller' => $this->getRouter()->getController(), 'action' => 'manage'],
          ['id' => $model->id],
        )];
      }
    }

    $form->setReturnUrl(
      $this->getRouter()->assemble([
        'controller' => $this->getRouter()->getController(),
        'action' => 'index'
      ])
    );

    $this->getView()->setVars([
      'icon' => $this->getAdminMenuItem()['icon'],
      'title' => $this->getTitle(),
      'form' => $form,
      'mode' => 'manage'
    ]);

    $this->getView()->setScript('form/index');
  }
}
