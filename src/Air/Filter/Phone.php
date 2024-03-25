<?php

namespace Air\Filter;

/**
 * Class Phone
 * @package Air\Filter
 */
class Phone extends FilterAbstract
{
  /**
   * @param $value
   * @return mixed|string
   */
  public function filter($value)
  {
    return '+' . trim(str_replace(['+', '-', ' ', '-', '(', ')', '.', ','], '', $value));
  }
}
