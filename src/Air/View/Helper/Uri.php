<?php

declare(strict_types=1);

namespace Air\View\Helper;

use Air\Core\Exception\ClassWasNotFound;
use Air\Core\Exception\DomainMustBeProvided;
use Air\Core\Exception\RouterVarMustBeProvided;
use Air\Core\Front;

class Uri extends HelperAbstract
{
  /**
   * @param array $route
   * @param array $params
   * @param bool $reset
   * @return string
   * @throws DomainMustBeProvided
   * @throws RouterVarMustBeProvided
   * @throws ClassWasNotFound
   */
  public function call(array $route = [], array $params = [], bool $reset = true): string
  {
    return Front::getInstance()->getRouter()->assemble($route, $params, $reset);
  }
}
