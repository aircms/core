<?php

declare(strict_types=1);

namespace Air\Model\Exception;

use Exception;

class DriverClassDoesNotExtendsFromDriverAbstract extends Exception
{
  /**
   * @param string $className
   */
  public function __construct(string $className)
  {
    parent::__construct("DriverClassDoesNotExtendsFromDriverAbstract: " . $className);
  }
}