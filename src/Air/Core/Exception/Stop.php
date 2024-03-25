<?php

namespace Air\Core\Exception;

use Exception;

/**
 * Class Stop
 * @package Air\Exception
 */
class Stop extends Exception
{
  /**
   * Stop constructor.
   */
  public function __construct()
  {
    parent::__construct();
  }
}