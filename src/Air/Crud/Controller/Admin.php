<?php

declare(strict_types=1);

namespace Air\Crud\Controller;

use Air\Crud\Locale;
use Air\Form\Element\Checkbox;
use Air\Form\Form;
use Exception;
use Air\Core\Exception\DomainMustBeProvided;
use Air\Core\Exception\RouterVarMustBeProvided;
use Air\Core\Exception\ValidatorClassWasNotFound;
use Air\Crud\Controller\Multiple;
use Air\Form\Element\Text;
use Air\Form\Generator;

/**
 * @mod-manageable true
 */
class Admin extends Multiple
{
  /**
   * @return array[]
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
   * @throws \Air\Core\Exception\ClassWasNotFound
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
   * @param ModelAbstract $model
   * @return Generator
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
        new Text('password', [
          'value' => '',
          'label' => Locale::t('Password'),
          'allowNull' => true,
        ]),
      ]
    ]);
  }

  /**
   * @param string|null $id
   * @throws DomainMustBeProvided
   * @throws RouterVarMustBeProvided|ValidatorClassWasNotFound
   * @throws Exception
   */
  public function manage(string $id = null): void
  {
    $model = \Air\Crud\Model\Admin::fetchObject([
      'id' => $this->getRequest()->getParam('id')
    ]);

    $form = $this->getForm($model);

    if ($this->getRequest()->isPost()) {
      if ($form->isValid($this->getRequest()->getPostAll())) {

        $formData = $form->getValues();

        if (strlen($formData['password'])) {
          $formData['password'] = md5($formData['password']);
        } else {
          unset($formData['password']);
        }

        $model->populate($formData);
        $model->save();

        die('ok:' . $this->getRequest()->getPost('return-url'));
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
