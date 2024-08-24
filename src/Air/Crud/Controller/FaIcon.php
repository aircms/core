<?php

declare(strict_types=1);

namespace Air\Crud\Controller;

class FaIcon extends Multiple
{
  public function select(): void
  {
    $this->getView()->assign('isSelectControl', true);
    $this->getView()->setScript('fa-icon');
  }
}
