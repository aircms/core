<?php

declare(strict_types=1);

namespace Air\View\Helper;

class Charset extends HelperAbstract
{
  /**
   * @param string $charset
   * @return string
   */
  public function call(string $charset = 'UTF-8'): string
  {
    return '<meta charset="' . $charset . '">';
  }
}
