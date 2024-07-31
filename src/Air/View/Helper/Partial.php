<?php

declare(strict_types=1);

namespace Air\View\Helper;

use Air\Core\Exception\ClassWasNotFound;
use Exception;

class Partial extends HelperAbstract
{
  /**
   * @param string $template
   * @param array $vars
   * @return string
   * @throws ClassWasNotFound
   * @throws Exception
   */
  public function call(string $template, array $vars = []): string
  {
    foreach ($vars as $key => $value) {
      $this->getView()->assign($key, $value);
    }
    return $this->getView()->render($template);
   // return $this->getView()->render($template, $vars);
  }
}