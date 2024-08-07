<?php

declare(strict_types=1);

namespace Air\Filter;

/**
 * Class FilterAbstract
 */
abstract class FilterAbstract
{
  /**
   * FilterAbstract constructor.
   * @param array $options
   */
  public function __construct(array $options = [])
  {
    foreach ($options as $name => $value) {

      if (is_callable([$this, 'set' . ucfirst($name)])) {
        call_user_func_array([$this, 'set' . ucfirst($name)], [$value]);
      }
    }
  }

  /**
   * @param mixed $value
   * @param array $options
   * @return mixed
   */
  public static function clean(mixed $value, array $options = []): mixed
  {
    $filter = new static($options);
    return $filter->filter($value);
  }

  /**
   * @param $value
   * @return mixed|null
   */
  public abstract function filter($value);
}