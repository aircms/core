<?php

declare(strict_types=1);

namespace Air\View\Helper;

use Air\Core\Exception\ClassWasNotFound;
use Air\Core\Front;
use Air\Crud\Controller\FontsUi;
use Air\Crud\Model\Codes;
use Air\Crud\Model\Font;

class Head extends HelperAbstract
{
  /**
   * @param string $charset
   * @param string $viewport
   * @param string|null $favicon
   * @return string
   * @throws ClassWasNotFound
   */
  public function call(
    string       $charset = 'UTF-8',
    string       $viewport = 'width=device-width, initial-scale=1.0, minimum-scale=1.0',
    string       $favicon = null,
    string|array $assets = null
  ): string
  {
    $baseHref = Front::getInstance()->getConfig()['air']['asset']['prefix'];

    $head = [
      Codes::render(),
      tag('meta', attributes: ['charset' => $charset]),
      tag('meta', attributes: ['name' => 'viewport', 'content' => $viewport]),
      tag('base', attributes: ['href' => $baseHref . '/']),
      $this->getView()->getMeta()
    ];

    if (Font::quantity()) {
      $head[] = tag('link', attributes: [
        'rel' => 'stylesheet',
        'href' => Font::href()
      ]);
    }

    if ($favicon) {
      $faviconNameParts = explode('.', $favicon);
      $ext = $faviconNameParts[count($faviconNameParts) - 1];

      $head[] = tag('link', attributes: [
        'rel' => 'icon',
        'type' => 'image/' . $ext,
        'href' => $this->getView()->asset($favicon)
      ]);
    }

    if ($assets) {
      $head[] = $this->getView()->asset($assets);
    }

    return implode('', array_filter($head));
  }
}
