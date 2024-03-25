<?php

declare(strict_types=1);

namespace Air\Crud;

use Air\Cookie;
use Air\Core\Exception\ClassWasNotFound;
use Air\Core\Front;
use Air\Crud\Controller\Multiple;

class Storage extends Multiple
{
  /**
   * @return void
   * @throws ClassWasNotFound
   */
  public function index(): void
  {
    $storageConfig = Front::getInstance()->getConfig()['air']['storage'];
    $theme = Cookie::get('theme') ?? 'dark';

    $this->getView()->assign('storageConfig', $storageConfig);
    $this->getView()->assign('theme', $theme);

    $this->getView()->setScript('storage');
  }
}
