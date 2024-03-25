<?php

namespace Air\Core\Exception;

/**
 * Class ControllerClassWasNotFound
 * @package Air\Exception
 */
class ControllerClassWasNotFound extends \Exception
{
  /**
   * ControllerClassWasNotFound constructor.
   * @param string $controllerClassName
   */
  public function __construct(string $controllerClassName)
  {
    parent::__construct('ControllerClassWasNotFound: ' . $controllerClassName, 404);
  }
}