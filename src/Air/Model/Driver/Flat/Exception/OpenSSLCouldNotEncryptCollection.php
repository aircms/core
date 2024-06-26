<?php

namespace Air\Model\Driver\Flat\Exception;

use Exception;

/**
 * Class OpenSSLCouldNotEncryptCollection
 * @package Air\Model\Driver\Flat\Exception
 */
class OpenSSLCouldNotEncryptCollection extends Exception
{
  /**
   * OpenSSLCouldNotEncryptCollection constructor.
   * @param array $settings
   */
  public function __construct(array $settings)
  {
    parent::__construct("OpenSSLCouldNotEncryptCollection: " . var_export($settings, true));
  }
}