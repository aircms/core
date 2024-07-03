<?php

declare(strict_types=1);

namespace Air\View\Helper;

use Air\Core\Exception\ClassWasNotFound;
use Air\Core\Exception\DomainMustBeProvided;
use Air\Core\Exception\RouterVarMustBeProvided;
use Air\Core\Front;

class Meta extends HelperAbstract
{
  /**
   * @return string|void
   */
  public function call()
  {
    $meta = $this->getView()->getMeta();
    if (!$meta) {
      $defaultMetaClosure = $this->getView()->getDefaultMeta();
      if ($defaultMetaClosure) {
        $meta = $defaultMetaClosure();
      }
    }
    if ($meta) {
      return $meta->__toString();
    }
  }
}
