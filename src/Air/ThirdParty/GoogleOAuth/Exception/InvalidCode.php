<?php

declare(strict_types=1);

namespace Air\ThirdParty\GoogleOAuth\Exception;

use Exception;

class InvalidCode extends Exception
{
  /**
   * @param string $code
   */
  public function __construct(string $code)
  {
    parent::__construct('InvalidCode: ' . $code, 400);
  }
}