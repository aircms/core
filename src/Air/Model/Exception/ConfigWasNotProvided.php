<?php

declare(strict_types=1);

namespace Air\Model\Exception;

use Exception;

class ConfigWasNotProvided extends Exception
{
  public function __construct()
  {
    parent::__construct("ConfigWasNotProvided");
  }
}