<?php

namespace Air\Core\Exception;

use Exception;

/**
 * Class RequestMethodIsNotSupported
 * @package Air\Exception
 */
class RequestMethodIsNotSupported extends Exception
{
  /**
   * RequestMethodIsNotSupported constructor.
   * @param string $method
   */
  public function __construct(string $method)
  {
    parent::__construct('RequestMethodIsNotSupported: ' . $method, 500);
  }
}