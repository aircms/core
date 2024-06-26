<?php

namespace Air\Model\Driver\Flat\Query\Exception;

use Exception;

/**
 * Class OperatorNinRequiresArray
 * @package Air\Model\Driver\Flat\Query\Exception
 */
class OperatorNinRequiresArray extends Exception
{
  /**
   * OperatorNinRequiresArray constructor.
   * @param mixed $operatorValue
   */
  public function __construct($operatorValue)
  {
    parent::__construct("OperatorNinRequiresArray: " . $operatorValue);
  }
}