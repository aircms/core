<?php

declare(strict_types=1);

namespace Air\Crud\Controller\MultipleHelper;

trait Select
{
  public function select(): void
  {
    $this->getView()->setVars([
      'icon' => $this->getIcon(),
      'title' => $this->getTitle(),
      'filter' => $this->getFilterWithValues(),
      'header' => $this->getHeader(),
      'paginator' => $this->getPaginator(),
      'controller' => $this->getRouter()->getController(),
      'controls' => $this->getControls(),
      'isSelectControl' => true,
    ]);
    $this->getView()->setScript('table/index');
  }
}