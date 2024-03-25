<?php

namespace Air\Filter;

/**
 * Class Lowercase
 * @package Air\Filter
 */
class Lowercase extends FilterAbstract
{
  /**
   * @param $value
   * @return mixed|string
   */
  public function filter($value)
  {
    return strtolower($value);
  }
}