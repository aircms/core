<?php

declare(strict_types=1);

namespace Air\Crud\Controller;

class Cache extends AuthCrud
{
  /**
   * @return void
   */
  public function index(): void
  {
    $this->getView()->setLayoutEnabled(false);
    $this->getView()->setAutoRender(false);

    \Air\Cache::remove();
  }
}
