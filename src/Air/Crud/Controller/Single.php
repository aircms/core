<?php

declare(strict_types=1);

namespace Air\Crud\Controller;

use Air\Model\ModelAbstract;

abstract class Single extends Multiple
{
  public function index()
  {
    $this->getRequest()->setGetParam('quick-save', true, true);
    $this->getView()->assign('isSingleControl', true);

    /** @var ModelAbstract $modelClassName */
    $modelClassName = $this->getModelClassName();
    return parent::manage($modelClassName::fetchObject()->id);
  }

  public function manage(string $id = null): void
  {
    $this->redirect($this->getRouter()->assemble(['controller' => $this->getRouter()->getController()], [], true));
  }
}
