<?php

declare(strict_types=1);

namespace Air\Crud\Admin;

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
 * @mod-title Users
 * @mod-manageable true
 *
 * @mod-header {"title": "Name", "by": "name"}
 * @mod-header {"title": "Login", "by": "login"}
 * @mod-header {"title": "Activity", "type": "bool", "by": "enabled"}
 */
class Controller extends Multiple
{
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
    return ModelAbstract::class;
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
          'label' => 'Enabled',
        ]),
        new Text('name', [
          'label' => 'Name',
        ]),
        new Text('login', [
          'label' => 'Login',
        ]),
        new Text('password', [
          'value' => '',
          'label' => 'Password',
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
    $model = ModelAbstract::fetchObject([
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
