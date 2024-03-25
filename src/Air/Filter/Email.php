<?php

namespace Air\Filter;

/**
 * Class Email
 * @package Air\Filter
 */
class Email extends FilterAbstract
{
  /**
   * @param $value
   * @return mixed|string
   */
  public function filter($value)
  {
    return trim(strtolower($value));
  }
}
