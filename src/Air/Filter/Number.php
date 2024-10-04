<?php

namespace Air\Filter;

/**
 * Class Trim
 * @package Air\Filter
 */
class Number extends FilterAbstract
{
  /**
   * @param $value
   * @return int
   */
  public function filter($value)
  {
    return intval($value ?? 0);
  }
}
