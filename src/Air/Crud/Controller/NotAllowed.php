<?php

declare(strict_types=1);

namespace Air\Crud\Controller;

use Air\Core\Controller;

/**
 * @mod-manageable true
 */
class NotAllowed extends Controller
{
  /**
   * @return void
   */
  public function index(): void
  {
    if ($this->getRequest()->isAjax()) {
      $this->getView()->setLayoutEnabled(false);
    } else {
      $this->getView()->setLayoutTemplate('index');
      $this->getView()->setLayoutEnabled(true);
    }

    $this->getView()->setAutoRender(true);
    $this->getView()->setPath(realpath(__DIR__ . '/../View'));
    $this->getView()->setScript('not-allowed');
  }
}
