<?php

declare(strict_types=1);

namespace Air\Crud\Controller;

use Air\Core\Controller;
use Air\Core\Front;

class Asset extends Controller
{
  /**
   * @var array
   */
  private array $files = [];

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
    $files = null;
    foreach ($this->files as $file) {
      $files .= $this->minify_css(file_get_contents($file));
    }

    $this->getResponse()->setHeader('Content-Type', 'text/css');
    return $files;
  }

  /**
   * @return string
   */
  public function js(): string
  {
    $files = null;
    foreach ($this->files as $file) {
      $files .= $this->minimizeJavascriptSimple(file_get_contents($file));
    }

    $this->getResponse()->setHeader('Content-Type', 'application/javascript');
    return $files;
  }

  function minify_css($css) {
    // Remove comments
    $css = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css);
    // Remove spaces before and after selectors, braces, and colons
    $css = preg_replace('/\s*([{}|:;,])\s+/', '$1', $css);
    // Remove remaining spaces and line breaks
    $css = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '',$css);

    return $css;
  }

  function minimizeJavascriptSimple($javascript){
    return preg_replace(array("/\s+\n/", "/\n\s+/", "/ +/"), array("\n", "\n ", " "), $javascript);
  }
}
