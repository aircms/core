<?php

declare(strict_types=1);

namespace Air\Crud;

use Air\Core\Exception\ClassWasNotFound;
use Air\Core\Front;

class Locale
{
  /**
   * @var array|null
   */
  public static ?array $keys = null;

  /**
   * @param string $key
   * @return string
   * @throws ClassWasNotFound
   */
  public static function t(string $key): string
  {
    if (!self::$keys) {
      self::$keys = self::phrases();
    }
    return self::$keys[$key] ?? $key;
  }

  /**
   * @return array
   * @throws ClassWasNotFound
   */
  public static function phrases(): array
  {
    $lang = Front::getInstance()->getConfig()['air']['admin']['locale'];
    $filename = realpath(__DIR__ . '/../../../locale/' . $lang . '.php');
    return require $filename;
  }
}