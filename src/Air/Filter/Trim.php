<?php

namespace Air\Filter;

/**
 * Class Trim
 * @package Air\Filter
 */
class Trim extends FilterAbstract
{
  /**
   * @param $value
   * @return mixed|string
   */
  public function filter($value)
  {
    $value = $value ?? '';

    return trim($value);
  }
}
