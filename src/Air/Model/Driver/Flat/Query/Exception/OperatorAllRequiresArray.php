<?php

namespace Air\Model\Driver\Flat\Query\Exception;

use Exception;

/**
 * Class OperatorAllRequiresArray
 * @package Air\Model\Driver\Flat\Query\Exception
 */
class OperatorAllRequiresArray extends Exception
{
  /**
   * OperatorAllRequiresArray constructor.
   * @param mixed $operatorValue
   */
  public function __construct($operatorValue)
  {
    parent::__construct("OperatorAllRequiresArray: " . $operatorValue);
  }
}