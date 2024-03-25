<?php

namespace Air\Model\Driver\Flat\Query\Exception;

use Exception;

/**
 * Class OperatorInRequiresArray
 * @package Air\Model\Driver\Flat\Query\Exception
 */
class OperatorInRequiresArray extends Exception
{
  /**
   * OperatorInRequiresArray constructor.
   * @param mixed $operatorValue
   */
  public function __construct($operatorValue)
  {
    parent::__construct("OperatorInRequiresArray: " . $operatorValue);
  }
}