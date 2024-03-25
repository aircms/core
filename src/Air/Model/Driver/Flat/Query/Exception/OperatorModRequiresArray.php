<?php

namespace Air\Model\Driver\Flat\Query\Exception;

use Exception;

/**
 * Class OperatorModRequiresArray
 * @package Air\Model\Driver\Flat\Query\Exception
 */
class OperatorModRequiresArray extends Exception
{
  /**
   * OperatorModRequiresArray constructor.
   * @param mixed $operatorValue
   */
  public function __construct($operatorValue)
  {
    parent::__construct("OperatorModRequiresArray: " . $operatorValue);
  }
}