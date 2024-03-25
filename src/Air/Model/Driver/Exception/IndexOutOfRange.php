<?php

declare(strict_types=1);

namespace Air\Model\Driver\Exception;

use Exception;

class IndexOutOfRange extends Exception
{
  /**
   * @param int $index
   * @param int $maxValue
   */
  public function __construct(int $index, int $maxValue)
  {
    parent::__construct("IndexOutOfRange: " . $index . ', max value is ' . $maxValue);
  }
}