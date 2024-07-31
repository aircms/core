<?php

declare(strict_types=1);

namespace Air\Type;

use ReflectionProperty;
use Throwable;

abstract class TypeAbstract
{
  /**
   * @param array|null $item
   */
  public function __construct(?array $item = [])
  {
    foreach (array_keys(get_class_vars(static::class)) as $var) {
      if (!empty($item[$var])) {
        try {
          $rp = new ReflectionProperty(static::class, $var);
          $className = $rp->getType()->getName();
          $this->{$var} = new $className($item[$var]);

        } catch (Throwable) {
        }
        if (!$this->{$var}) {
          $this->{$var} = $item[$var];
        }
      }
    }
  }
}