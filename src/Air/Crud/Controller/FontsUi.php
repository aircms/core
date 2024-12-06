<?php

declare(strict_types=1);

namespace Air\Crud\Controller;

use Air\Core\Controller;

class FontsUi extends Controller
{
  public function index(): string
  {
    $this->getView()->setPath(realpath(__DIR__ . '/../View'));
    $this->getResponse()->setHeader('Content-type', 'text/css');

    $css = [];
    foreach (\Air\Crud\Model\Font::all() as $font) {
      $css[] = $font->asCss();
    }
    return implode('', $css);
  }
}
