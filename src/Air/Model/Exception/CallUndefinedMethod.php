<?php

declare(strict_types=1);

namespace Air\Model\Exception;

use Exception;

class CallUndefinedMethod extends Exception
{
  /**
   * @param string $className
   * @param string $methodName
   */
  public function __construct(string $className, string $methodName)
  {
    parent::__construct('CallUndefinedMethod: ' . $className . '::' . $methodName);
  }
}