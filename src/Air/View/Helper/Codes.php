<?php

declare(strict_types=1);

namespace Air\View\Helper;

use Air\Core\Exception\ClassWasNotFound;
use Air\Core\Exception\DomainMustBeProvided;
use Air\Core\Exception\RouterVarMustBeProvided;
use Air\Core\Front;
use Air\Map;

class Codes extends HelperAbstract
{
  /**
   * @return string|void
   */
  public function call()
  {
    return implode('', Map::execute(\Air\Crud\Model\Codes::all(), 'description'));
  }
}
