<?php

namespace Air\Core\Exception;

/**
 * Class ActionMethodWasNotFound
 * @package Air\Exception
 */
class ActionMethodWasNotFound extends \Exception
{
  /**
   * ActionMethodWasNotFound constructor.
   * @param string $actionClassName
   */
  public function __construct(string $actionClassName)
  {
    parent::__construct('ActionMethodWasNotFound: ' . $actionClassName, 404);
  }
}