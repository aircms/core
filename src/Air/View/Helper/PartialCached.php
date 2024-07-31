<?php

declare(strict_types=1);

namespace Air\View\Helper;

use Air\Cache;
use Air\Core\Exception\ClassWasNotFound;

class PartialCached extends HelperAbstract
{
  /**
   * @param string $template
   * @return string
   * @throws ClassWasNotFound
   */
  public function call(string $template): string
  {
    return Cache::single([$template], function () use ($template) {
      return $this->getView()->render($template);
    });
  }
}