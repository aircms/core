<?php

declare(strict_types=1);

namespace Air\Type;

abstract class TypeAbstract
{
  /**
   * @param array|null $item
   */
  public function __construct(?array $item = [])
  {
    foreach (array_keys(get_class_vars(static::class)) as $var) {
      if (!empty($item[$var])) {
        $this->{$var} = $item[$var];
      }
    }
  }
}