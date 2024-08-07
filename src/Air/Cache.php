<?php

declare(strict_types=1);

namespace Air;

use Air\Core\Exception\ClassWasNotFound;
use Air\Core\Front;
use Air\Crud\Model\Language;
use Air\Model\Exception\CallUndefinedMethod;
use Air\Model\Exception\ConfigWasNotProvided;
use Air\Model\Exception\DriverClassDoesNotExists;
use Air\Model\Exception\DriverClassDoesNotExtendsFromDriverAbstract;
use Air\Model\ModelAbstract;
use Closure;
use Throwable;

/**
 * @collection AirCache
 *
 * @property string $id
 *
 * @property string $key
 * @property string $value
 * @property integer $lifetime
 */
class Cache extends ModelAbstract
{
  /**
   * @var array
   */
  private static array $single = [];

  /**
   * 1 day
   */
  const int LIFETIME_LONG = 86400;

  /**
   * 2 hours
   */
  const int LIFETIME_TEMP = 7200;

  /**
   * 30 minutes
   */
  const int LIFETIME_FAST = 1800;

  /**
   * 10 minutes
   */
  const int LIFETIME_QUICK = 600;

  /**
   * @param mixed $key
   * @param Closure $fn
   * @return mixed
   */
  public static function single(mixed $key, Closure $fn): mixed
  {
    $key = md5(serialize($key));
    if (isset(self::$single[$key])) {
      return self::$single[$key];
    }
    self::$single[$key] = $fn();
    return self::$single[$key];
  }

  /**
   * 10 minutes
   *
   * @param mixed $key
   * @param Closure $fn
   * @return mixed
   * @throws CallUndefinedMethod
   * @throws ClassWasNotFound
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   */
  public static function quick(mixed $key, Closure $fn): mixed
  {
    $lifetime = Front::getInstance()->getConfig()['air']['cache']['quick'] ?? self::LIFETIME_QUICK;
    return self::content($key, $lifetime, $fn);
  }

  /**
   * 30 minutes
   *
   * @param mixed $key
   * @param Closure $fn
   * @return mixed
   * @throws CallUndefinedMethod
   * @throws ClassWasNotFound
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   */
  public static function fast(mixed $key, Closure $fn): mixed
  {
    $lifetime = Front::getInstance()->getConfig()['air']['cache']['fast'] ?? self::LIFETIME_FAST;
    return self::content($key, $lifetime, $fn);
  }

  /**
   * 2 hours
   *
   * @param mixed $key
   * @param Closure $fn
   * @return mixed
   * @throws CallUndefinedMethod
   * @throws ClassWasNotFound
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   */
  public static function temp(mixed $key, Closure $fn): mixed
  {
    $lifetime = Front::getInstance()->getConfig()['air']['cache']['temp'] ?? self::LIFETIME_TEMP;
    return self::content($key, $lifetime, $fn);
  }

  /**
   * 1 day
   *
   * @param mixed $key
   * @param Closure $fn
   * @return mixed
   * @throws CallUndefinedMethod
   * @throws ClassWasNotFound
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   */
  public static function long(mixed $key, Closure $fn): mixed
  {
    $lifetime = Front::getInstance()->getConfig()['air']['cache']['long'] ?? self::LIFETIME_LONG;
    return self::content($key, $lifetime, $fn);
  }

  /**
   * @param mixed $key
   * @param int $lifetime
   * @param Closure $fn
   * @return mixed
   * @throws CallUndefinedMethod
   * @throws ClassWasNotFound
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   */
  public static function content(mixed $key, int $lifetime, Closure $fn): mixed
  {
    if (!isset($key['language'])) {
      try {
        $key = (array)$key;
        $key['language'] = Language::getLanguage()->key;
      } catch (Throwable) {
      }
    }
    return self::propagate($key, $lifetime, $fn);
  }

  /**
   * @param mixed $key
   * @param int $lifetime
   * @param Closure|null $fn
   * @return mixed
   * @throws Core\Exception\ClassWasNotFound
   * @throws Model\Exception\CallUndefinedMethod
   * @throws Model\Exception\ConfigWasNotProvided
   * @throws Model\Exception\DriverClassDoesNotExists
   * @throws Model\Exception\DriverClassDoesNotExtendsFromDriverAbstract
   */
  public static function propagate(mixed $key, int $lifetime, Closure $fn = null): mixed
  {
    if (!(Front::getInstance()->getConfig()['air']['cache']['enabled'] ?? false)) {
      return self::getContent($fn);
    }

    $key = md5(serialize($key));
    $data = self::one(['key' => $key]);

    if ($data) {
      if ($data->lifetime >= time()) {
        return json_decode($data->value, true);
      }
      $data->remove();
    }

    $content = self::getContent($fn);

    if (!$content) {
      return $content;
    }

    $cache = new self();
    $cache->key = $key;
    $cache->value = json_encode($content);
    $cache->lifetime = time() + $lifetime;

    $cache->save();
    return $content;
  }

  /**
   * @param Closure $fn
   * @return mixed
   */
  public static function getContent(Closure $fn): mixed
  {
    ob_start();

    $returned = $fn();
    $content = ob_get_contents();

    ob_end_clean();

    return $returned ?? $content;
  }
}