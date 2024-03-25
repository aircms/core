<?php

declare(strict_types=1);

namespace Air;

use Closure;
use Air\Model\ModelAbstract;

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
   * @param mixed $key
   * @param Closure|null $fn
   * @return mixed
   * @throws Core\Exception\ClassWasNotFound
   * @throws Model\Exception\CallUndefinedMethod
   * @throws Model\Exception\ConfigWasNotProvided
   * @throws Model\Exception\DriverClassDoesNotExists
   * @throws Model\Exception\DriverClassDoesNotExtendsFromDriverAbstract
   */
  public static function propagate(mixed $key, Closure $fn = null): mixed
  {
    $key = md5(var_export($key, true));
    $data = self::one(['key' => $key]);

    if ($data) {
      return json_decode($data->value, true);
    }

    $cache = new self();
    $cache->key = $key;
    $cache->value = json_encode($fn());

    $cache->save();
    return json_decode($cache->value, true);
  }
}
