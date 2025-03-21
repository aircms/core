<?php

declare(strict_types=1);

namespace Air\Crud\Controller;

use Air\Core\Controller;
use Air\Core\Front;

class FontsUi extends Controller
{
  public static function asset(): string
  {
    return tag(tagName: 'link', attributes: [
      'href' => '/' . Front::getInstance()->getConfig()['air']['fontsUi'],
      'rel' => 'stylesheet'
    ]);
  }

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
