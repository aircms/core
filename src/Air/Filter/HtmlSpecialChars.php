<?php

namespace Air\Filter;

/**
 * Class HtmlSpecialChars
 * @package Air\Filter
 */
class HtmlSpecialChars extends FilterAbstract
{
  /**
   * @param $value
   * @return mixed|string
   */
  public function filter($value)
  {
    return htmlspecialchars($value ?? '');
  }
}