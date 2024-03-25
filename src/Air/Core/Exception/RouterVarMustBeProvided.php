<?php

namespace Air\Core\Exception;

use Exception;

/**
 * Class RouterVarMustBeProvided
 * @package Air\Exception
 */
class RouterVarMustBeProvided extends Exception
{
  /**
   * RouterVarMustBeProvided constructor.
   * @param string $var
   */
  public function __construct(string $var)
  {
    parent::__construct('RouterVarMustBeProvided: ' . $var, 500);
  }
}