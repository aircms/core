<?php

namespace Air\Model\Driver\Flat\Query\Exception;

use Exception;

/**
 * Class LogicalOperatorRequiresNonEmptyArray
 * @package Air\Model\Driver\Flat\Query\Exception
 */
class LogicalOperatorRequiresNonEmptyArray extends Exception
{
  /**
   * LogicalOperatorRequiresNonEmptyArray constructor.
   * @param string $logicalOperator
   */
  public function __construct(string $logicalOperator)
  {
    parent::__construct("LogicalOperatorRequiresNonEmptyArray: " . $logicalOperator);
  }
}