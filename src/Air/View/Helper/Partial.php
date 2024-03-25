<?php

declare(strict_types=1);

namespace Air\View\Helper;

use Air\Core\Exception\ClassWasNotFound;

class Partial extends HelperAbstract
{
  /**
   * @param $template
   * @param array $vars
   * @return string
   * @throws ClassWasNotFound
   */
  public function call($template, array $vars = []): string
  {
    return $this->getView()->render($template, $vars);
  }
}