<?php

namespace Air\Core\Exception;

use Exception;

/**
 * Class DomainMustBeProvided
 * @package Air\Exception
 */
class DomainMustBeProvided extends Exception
{
  /**
   * DomainMustBeProvided constructor.
   */
  public function __construct()
  {
    parent::__construct('DomainMustBeProvided: ', 500);
  }
}