<?php

namespace Air\Core\Exception;

/**
 * Class ActionMethodIsReserved
 * @package Air\Exception
 */
class ActionMethodIsReserved extends \Exception
{
  /**
   * ActionMethodIsReserved constructor.
   * @param string $actionClassName
   */
  public function __construct(string $actionClassName)
  {
    parent::__construct('ActionMethodIsReserved: ' . $actionClassName, 404);
  }
}