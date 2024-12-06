<?php

declare(strict_types=1);

namespace Air\Crud\Controller\MultipleHelper;

use Air\Model\ModelAbstract;

trait View
{
  public function view(string $id): void
  {
    /** @var ModelAbstract $modelClassName */
    $modelClassName = $this->getModelClassName();
    $model = $modelClassName::fetchObject(['id' => $id]);

    $form = $this->getForm($model);

    $form->setReturnUrl($this->getRouter()->assemble([
      'controller' => $this->getRouter()->getController(),
    ]));

    $this->getView()->setVars([
      'icon' => $this->getAdminMenuItem()['icon'] ?? null,
      'title' => $this->getTitle(),
      'form' => $form,
      'mode' => 'manage',
      'isQuickManage' => true,
      'isSelectControl' => true,
    ]);

    $this->getView()->setScript('form/index');
  }
}