<?php

declare(strict_types=1);

namespace Air\Crud\View\Helper;

use Air\View\ViewHelper\HelperAbstract;

class Paginator extends HelperAbstract
{
  /**
   * @param \Air\Model\Paginator $paginator
   * @return mixed
   */
  public function call(\Air\Model\Paginator $paginator): mixed
  {
    return $this->getView()->partial('table/partial/pagination', [
      'paginator' => $paginator
    ]);
  }
}
