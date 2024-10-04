<?php

declare(strict_types=1);

namespace Air\ThirdParty\GoogleOAuth\Exception;

use Exception;

class UnableToGetUserByAccessToken extends Exception
{
  /**
   * @param string $accessToken
   */
  public function __construct(string $accessToken)
  {
    parent::__construct('UnableToGetUserByAccessToken: ' . $accessToken, 400);
  }
}