<?php

declare(strict_types=1);

namespace Air;

use Air\Core\Exception\ClassWasNotFound;
use Air\Core\Front;
use Throwable;

class Cookie
{
  /**
   * @var string|null
   */
  public static ?string $namespace = null;

  /**
   * @return string|null
   */
  public static function getNamespace(): ?string
  {
    return self::$namespace;
  }

  /**
   * @param string|null $namespace
   */
  public static function setNamespace(?string $namespace): void
  {
    self::$namespace = $namespace;
  }

  /**
   * @param string $name
   * @param mixed $value
   * @return bool
   * @throws ClassWasNotFound
   */
  public static function set(string $name, mixed $value): bool
  {
    return self::_set($name, base64_encode(serialize($value)));
  }

  /**
   * @param string $name
   * @param string $value
   * @return bool
   * @throws ClassWasNotFound
   */
  private static function _set(string $name, string $value): bool
  {
    $name = self::_getName($name);

    $_COOKIE[$name] = $value;
    return setcookie($name, $value, time() * 2, '/');
  }

  /**
   * @param string $name
   * @return string
   * @throws ClassWasNotFound
   */
  private static function _getName(string $name): string
  {
    if (Front::getInstance()->getConfig()['air']['cookie'] ?? false) {
      self::setNamespace(Front::getInstance()->getConfig()['air']['cookie']);
    }

    if (!self::$namespace) {
      return $name;
    }

    return implode(':', [self::$namespace, $name]);
  }

  /**
   * @param string $name
   * @return mixed
   * @throws ClassWasNotFound
   */
  public static function get(string $name): mixed
  {
    $name = self::_getName($name);

    if ($value = self::_get($name)) {
      $base64Decoded = base64_decode($value);
      try {
        return unserialize($base64Decoded);
      } catch (Throwable) {
        try {
          return json_decode(urldecode($base64Decoded));
        } catch (Throwable) {
        }
      }
    }
    return null;
  }

  /**
   * @param string $name
   * @return null
   */
  private static function _get(string $name): mixed
  {
    return $_COOKIE[$name] ?? null;
  }

  /**
   * @param string $name
   * @return bool
   * @throws ClassWasNotFound
   */
  public static function remove(string $name): bool
  {
    $name = self::_getName($name);
    unset($_COOKIE[$name]);

    return setcookie($name, '', 1, '/');
  }
}
