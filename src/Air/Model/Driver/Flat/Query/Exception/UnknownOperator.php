<?php

namespace Air\Model\Driver\Flat\Query\Exception;

use Exception;

/**
 * Class UnknownOperator
 * @package Air\Model\Driver\Flat\Query\Exception
 */
class UnknownOperator extends Exception
{
  /**
   * UnknownOperator constructor.
   * @param mixed $operator
   */
  public function __construct($operator)
  {
    parent::__construct("UnknownOperator: " . $operator);
  }
}