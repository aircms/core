<?php

declare(strict_types=1);

namespace Air\Model;

use Air\Core\Exception\ClassWasNotFound;
use Air\Core\Front;

class Config
{
  /**
   * @var array
   */
  private static array $config = [];

  /**
   * @return array
   * @throws ClassWasNotFound
   */
  public static function getConfig(): array
  {
    if (!self::$config) {
      self::$config = Front::getInstance()->getConfig()['air']['db'];
    }

    return self::$config;
  }

  /**
   * @param array $config
   */
  public static function setConfig(array $config): void
  {
    self::$config = $config;
  }
}