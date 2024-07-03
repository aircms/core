<?php

declare(strict_types=1);

namespace Air\Crud;

use Air\Core\Front;

class Locale
{
  /**
   * @var array
   */
  public static ?array $keys = null;

  /**
   * @param string $key
   * @return string
   * @throws \Air\Core\Exception\ClassWasNotFound
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
   */
  public static function phrases(): array
  {
    $lang = Front::getInstance()->getConfig()['air']['admin']['locale'];
    $filename = realpath(__DIR__ . '/../../../locale/' . $lang . '.php');
    $phrases = require $filename;
    return $phrases;
  }
}