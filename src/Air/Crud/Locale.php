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
      $filename = __DIR__ . '/../../../locale/' . Front::getInstance()->getConfig()['air']['admin']['locale'] . '.json';
      self::$keys = json_decode(file_get_contents(realpath($filename)), true) ?? [];
    }

    if (!isset(self::$keys[$key])) {
      self::$keys[$key] = $key;

      $filename = __DIR__ . '/../../../locale/ua.json';
      file_put_contents($filename, json_encode(self::$keys, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

      $filename = __DIR__ . '/../../../locale/en.json';
      file_put_contents($filename, json_encode(self::$keys, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }
    return self::$keys[$key];
  }
}