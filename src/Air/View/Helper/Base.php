<?php

declare(strict_types=1);

namespace Air\View\Helper;

use Air\Core\Exception\ClassWasNotFound;
use Air\Core\Front;

class Base extends HelperAbstract
{
  /**
   * @return string
   * @throws ClassWasNotFound
   */
  public function call(): string
  {
    return tag('base', attributes: ['href' => Front::getInstance()->getConfig()['air']['asset']['prefix'] . '/']);
  }
}
