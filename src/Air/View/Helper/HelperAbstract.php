<?php

declare(strict_types=1);

namespace Air\View\Helper;

use Air\View\View;

abstract class HelperAbstract
{
  /**
   * @var View|null
   */
  protected ?View $_view = null;

  /**
   * @return View
   */
  public function getView(): View
  {
    return $this->_view;
  }

  /**
   * @param View $view
   */
  public function setView(View $view): void
  {
    $this->_view = $view;
  }
}