<?php

declare(strict_types=1);

namespace Air\Crud\Controller;

use Air\Cookie;
use Air\Core\Exception\ClassWasNotFound;
use Air\Core\Front;
use Exception;

class Storage extends Multiple
{
  /**
   * @return void
   * @throws ClassWasNotFound
   * @throws Exception
   */
  public function index(): void
  {
    $storageConfig = Front::getInstance()->getConfig()['air']['storage'];
    $theme = Cookie::get('theme') ?? 'dark';
    $lang = Front::getInstance()->getConfig()['air']['admin']['locale'] ?? 'en';

    $this->getView()->assign('storageConfig', $storageConfig);
    $this->getView()->assign('theme', $theme);
    $this->getView()->assign('lang', $lang);

    $this->getView()->setScript('storage');
  }
}
