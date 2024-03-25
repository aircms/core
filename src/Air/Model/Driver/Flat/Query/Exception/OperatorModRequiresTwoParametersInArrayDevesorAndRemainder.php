<?php

namespace Air\Model\Driver\Flat\Query\Exception;

use Exception;

/**
 * Class OperatorModRequiresTwoParametersInArrayDevesorAndRemainder
 * @package Air\Model\Driver\Flat\Query\Exception
 */
class OperatorModRequiresTwoParametersInArrayDevesorAndRemainder extends Exception
{
  /**
   * OperatorModRequiresTwoParametersInArrayDevesorAndRemainder constructor.
   */
  public function __construct()
  {
    parent::__construct("OperatorModRequiresTwoParametersInArrayDevesorAndRemainder: ");
  }
}