<?php

namespace Air\Model\Driver\Flat\Exception;

use Exception;

/**
 * Class OpenSSLFunctionDoesNotExists
 * @package Air\Model\Driver\Flat\Exception
 */
class OpenSSLFunctionDoesNotExists extends Exception
{
  /**
   * OpenSSLFunctionDoesNotExists constructor.
   *
   * @param string $opensslFunction
   */
  public function __construct(string $opensslFunction)
  {
    parent::__construct("OpenSSLFunctionDoesNotExists: " . $opensslFunction);
  }
}