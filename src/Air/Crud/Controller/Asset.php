<?php

declare(strict_types=1);

namespace Air\Crud\Controller;

use Air\Core\Controller;
use Air\Core\Front;
use MatthiasMullie\Minify\CSS;
use MatthiasMullie\Minify\JS;

class Asset extends Controller
{
  /**
   * @var array
   */
  private array $files = [];

  /**
   * @return void
   * @throws \Air\Core\Exception\ClassWasNotFound
   */
  public function init(): void
  {
    parent::init();

    $prefix = Front::getInstance()->getConfig()['air']['asset']['prefix'];
    $appFolder = realpath(Front::getInstance()->getConfig()['air']['loader']['path'] . '/../www');

    foreach (explode('|', base64_decode($this->getParam('s'))) as $css) {
      $this->files[] = $appFolder . $prefix . '/' . $css;
    }
  }

  /**
   * @return void
   */
  public function css(): string
  {
    $minifier = new CSS();
    $minifier->addFile($this->files);

    $this->getResponse()->setHeader('Content-Type', 'text/css');
    return $minifier->minify();
  }

  /**
   * @return string
   */
  public function js(): string
  {
    $minifier = new JS();
    $minifier->addFile($this->files);

    $this->getResponse()->setHeader('Content-Type', 'application/javascript');
    return $minifier->minify();
  }
}
