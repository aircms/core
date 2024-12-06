<?php

declare(strict_types=1);

namespace Air\Crud\Controller\MultipleHelper;

use Air\Model\ModelAbstract;
use Exception;

trait Position
{
  public function position()
  {
    /** @var ModelAbstract $modelClassName */
    $modelClassName = $this->getModelClassName();
    $model = new $modelClassName();

    if (!$model->getMeta()->hasProperty('position')) {
      throw new Exception('Model does not have position property');
    }

    $rows = $model::fetchAll($this->getConditions(), ['position' => 1], 100);

    if ($this->getRequest()->isPost()) {

      $this->getView()->setAutoRender(false);
      $this->getView()->setLayoutEnabled(false);

      foreach (($this->getParam('items') ?? []) as $index => $id) {
        $row = $model::fetchOne(['id' => $id]);
        $row->position = (int)$index;
        $row->save();
      }

      return $this->getRouter()->assemble(
        ['action' => 'index'],
        $this->getRequest()->getGetAll()
      );
    }

    $this->getView()->setVars([
      'icon' => $this->getIcon(),
      'title' => $this->getTitle(),

      'rows' => $rows,
      'positioning' => $this->getPositioning(),

      'params' => $this->getParams(),
      'controller' => $this->getRouter()->getController(),

      'isPositioningControl' => true
    ]);

    $this->getView()->setScript('table/position');
  }
}