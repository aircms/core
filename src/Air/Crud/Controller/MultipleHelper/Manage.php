<?php

declare(strict_types=1);

namespace Air\Crud\Controller\MultipleHelper;

use Air\Crud\Controller\Single;
use Air\Crud\Model\History;
use Air\Crud\Model\Language;
use Air\Form\Element\Hidden;
use Air\Form\Form;
use Air\Form\Generator;
use Air\Map;
use Air\Model\ModelAbstract;
use Air\Model\ModelInterface;
use Throwable;

trait Manage
{
  protected function getForm($model = null): Form
  {
    $formClassName = $this->getFormClassName();

    if (class_exists($formClassName)) {
      /** @var Form $formClassName */
      return new $formClassName(['data' => $model]);
    }

    return Generator::full($model);
  }

  protected function getFormMultiple(): Form
  {
    /** @var ModelAbstract $modelClassName */
    $modelClassName = $this->getModelClassName();
    $model = new $modelClassName();

    return Generator::full($model);
  }

  protected function saveModel($formData, $model): void
  {
    /** @var ModelAbstract $modelClassName */
    $modelClassName = $this->getModelClassName();

    $oldModel = new $modelClassName($model->getData());
    $oldData = [];

    foreach ($formData as $key => $value) {
      if ($model->{$key} instanceof ModelInterface) {
        $oldValue = $model->{$key}->id;
      } else {
        $oldValue = $model->{$key};
      }
      $oldData[$key] = $oldValue;
    }

    $this->adminLog(
      $model->id
        ? History::TYPE_WRITE_ENTITY
        : History::TYPE_CREATE_ENTITY,
      isset($oldData['title']) ? [$oldData['title']] : $oldData,
      null,
      $oldData,
      $formData
    );

    $model->populateWithoutQuerying($formData);
    $isCreating = !!$model->id;

    $model->save();

    if ($isCreating) {
      $this->didChanged($model, $formData, $oldModel);
    } else {
      $this->didCreated($model, $formData);
    }

    $this->didSaved($model, $formData, $oldModel);
  }

  public function manageMultiple()
  {
    $form = $this->getFormMultiple();
    $form->addElement(new Hidden('filter', [
      'value' => base64_encode(serialize($this->getParam('filter'))),
    ]));

    /** @var ModelAbstract $modelClassName */
    $modelClassName = $this->getModelClassName();

    if ($this->getRequest()->isPost()) {
      if ($form->isValid($this->getRequest()->getPostAll())) {

        $formData = $form->getCleanValues();

        $filter = unserialize(base64_decode($formData['filter']));
        unset($formData['filter']);

        $cond = $this->getConditions($filter);

        foreach ($modelClassName::fetchAll($cond) as $item) {
          $this->saveModel($formData, $item);
        }

        return ['count' => $modelClassName::count($cond)];

      } else {
        $this->getResponse()->setStatusCode(400);
      }
    }

    $returnUrl = $this->getParam('returnUrl');
    if (!$returnUrl) {
      $returnUrl = $this->getRouter()->assemble([
        'controller' => $this->getRouter()->getController(),
        'action' => 'index'
      ]);
    }

    $form->setReturnUrl($returnUrl);

    $count = $modelClassName::count($this->getConditions());

    $this->getView()->setVars([
      'icon' => $this->getAdminMenuItem()['icon'] ?? null,
      'title' => $this->getTitle() . ' (Manage ' . $count . ' records)',
      'form' => $form,
      'mode' => 'manage',
      'isQuickManage' => (bool)$this->getParam('isQuickManage') ?? false,
      'isSelectControl' => (bool)$this->getParam('isQuickManage') ?? false
    ]);

    $this->getView()->setScript('form/index');
  }

  public function manage(string $id = null)
  {
    /** @var ModelAbstract $modelClassName */
    $modelClassName = $this->getModelClassName();
    $model = $modelClassName::fetchObject(['id' => $id]);

    $form = $this->getForm($model);

    if ($this->getRequest()->isPost()) {
      if ($form->isValid($this->getRequest()->getPostAll())) {

        $isCreating = !$model->id;
        $formData = $form->getCleanValues();

        $this->saveModel($formData, $model);

        try {
          if ($isCreating && $languageProperty = $model->getMeta()->getPropertyWithName('language')) {
            if ($languageProperty->getType() === Language::class) {
              foreach (Language::fetchAll() as $language) {
                if ($language->id !== $formData['language']) {
                  $formData['language'] = $language->id;
                  $this->saveModel($formData, new $modelClassName());
                }
              }
            }
          }
        } catch (Throwable) {
        }

        $this->getView()->setLayoutEnabled(false);
        $this->getView()->setAutoRender(false);

        return [
          'url' => $this->getRouter()->assemble(
            ['controller' => $this->getEntity(), 'action' => 'manage'],
            ['returnUrl' => $this->getRequest()->getPost('return-url'), 'id' => $model->id]
          ),
          'quickSave' => $this->getParam('quick-save'),
          'newOne' => !$isCreating
        ];
      } else {
        $this->getResponse()->setStatusCode(400);
      }
    }

    if ($model->id) {
      try {
        $data = Map::execute($model, array_keys($this->getHeader()));
      } catch (Throwable) {
        $data = ['id' => $model->id];
      }
      $this->adminLog(History::TYPE_READ_ENTITY, $data);
    }

    $returnUrl = $this->getParam('returnUrl');
    if (!$returnUrl) {
      $returnUrl = $this->getRouter()->assemble([
        'controller' => $this->getRouter()->getController(),
        'action' => 'index'
      ]);
    }

    $form->setReturnUrl($returnUrl);

    $this->getView()->setVars([
      'icon' => $this->getAdminMenuItem()['icon'] ?? null,
      'title' => $this->getTitle(),
      'form' => $form,
      'mode' => 'manage',
      'isQuickManage' => (bool)$this->getParam('isQuickManage') ?? false,
      'isSelectControl' => (bool)$this->getParam('isQuickManage') ?? false,
      'isSingle' => is_subclass_of($this, Single::class)
    ]);

    $this->getView()->setScript('form/index');
  }
}