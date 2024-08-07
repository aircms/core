<?php

declare(strict_types=1);

namespace Air\Crud\Controller;

use Air\Core\Controller;
use Air\Core\Exception\ClassWasNotFound;
use Air\Core\Exception\DomainMustBeProvided;
use Air\Core\Front;
use Air\Crud\Auth;

abstract class AuthCrud extends Controller
{
  /**
   * @return void
   * @throws ClassWasNotFound
   * @throws DomainMustBeProvided
   */
  public function init(): void
  {
    parent::init();

    if (!Auth::getInstance()->isAuthorized()) {
      $this->redirect(
        $this->getRouter()->assemble([
          'controller' => Front::getInstance()->getConfig()['air']['admin']['auth']['route']
        ])
      );
    }

    if ($this->getRequest()->isAjax()) {
      $this->getView()->setLayoutEnabled(false);
    }
  }
}