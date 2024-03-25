<?php

namespace Air\Core\Exception;

/**
 * Class ClassWasNotFound
 * @package Air\Exception
 */
class ClassWasNotFound extends \Exception
{
  /**
   * ClassWasNotFound constructor.
   * @param string $className
   */
  public function __construct(string $className = null)
  {
    parent::__construct('ClassWasNotFound: ' . $className, 404);
  }
}