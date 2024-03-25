<?php

namespace Air\Filter;

/**
 * Class Uppercase
 * @package Air\Filter
 */
class Uppercase extends FilterAbstract
{
  /**
   * @param $value
   * @return mixed|string
   */
  public function filter($value)
  {
    return strtoupper($value);
  }
}