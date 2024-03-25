<?php

declare(strict_types=1);

namespace Air\Model\Driver\Exception;

use Exception;

class PropertyWasNotFound extends Exception
{
  /**
   * @param string $className
   * @param string $propertyName
   */
  public function __construct(string $className, string $propertyName)
  {
    parent::__construct("PropertyWasNotFound: " . $className . '::' . $propertyName);
  }
}