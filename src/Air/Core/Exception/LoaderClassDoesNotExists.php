<?php

namespace Air\Core\Exception;

use Exception;

/**
 * Class LoaderClassDoesNotExists
 * @package Air\Exception
 */
class LoaderClassDoesNotExists extends Exception
{
  /**
   * LoaderClassDoesNotExists constructor.
   * @param string $className
   */
  public function __construct(string $className)
  {
    parent::__construct('LoaderClassDoesNotExists:' . $className, 500);
  }
}