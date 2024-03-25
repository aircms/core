<?php

declare(strict_types=1);

namespace Air\Model\Exception;

use Exception;

class DriverClassDoesNotExists extends Exception
{
  /**
   * @param string $className
   */
  public function __construct(string $className)
  {
    parent::__construct("DriverClassDoesNotExists: " . $className);
  }
}