<?php

namespace Air\Core\Exception;

/**
 * Class SystemProcNotReadable
 * @package Air\Exception
 */
class SystemProcNotReadable extends \Exception
{
  /**
   * SystemProcNotReadable constructor.
   * @param string $proc
   */
  public function __construct(string $proc)
  {
    parent::__construct('SystemProcNotReadable: ' . $proc, 500);
  }
}