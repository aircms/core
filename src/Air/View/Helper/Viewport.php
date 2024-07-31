<?php

declare(strict_types=1);

namespace Air\View\Helper;

class Viewport extends HelperAbstract
{
  /**
   * @param string $viewport
   * @return string
   */
  public function call(string $viewport = 'width=device-width, initial-scale=1.0, minimum-scale=1.0'): string
  {
    return '<meta name="viewport" content="' . $viewport . '">';
  }
}
