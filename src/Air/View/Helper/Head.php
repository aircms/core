<?php

declare(strict_types=1);

namespace Air\View\Helper;

use Air\Core\Exception\ClassWasNotFound;
use Air\Core\Front;
use Air\Crud\Model\Codes;
use Air\Map;
use Air\Model\Exception\CallUndefinedMethod;
use Air\Model\Exception\ConfigWasNotProvided;
use Air\Model\Exception\DriverClassDoesNotExists;
use Air\Model\Exception\DriverClassDoesNotExtendsFromDriverAbstract;

class Head extends HelperAbstract
{
  /**
   * @param string $charset
   * @param string $viewport
   * @param string|null $favicon
   * @return string
   * @throws ClassWasNotFound
   * @throws CallUndefinedMethod
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   */
  public function call(
    string $charset = 'UTF-8',
    string $viewport = 'width=device-width, initial-scale=1.0, minimum-scale=1.0',
    string $favicon = null
  ): string
  {
    $baseHref = Front::getInstance()->getConfig()['air']['asset']['prefix'];
    $faviconNameParts = explode('.', $favicon);
    $ext = $faviconNameParts[count($faviconNameParts) - 1];

    $head = [
      tag('meta', attributes: ['charset' => $charset]),
      tag('meta', attributes: ['name' => 'viewport', 'content' => $viewport]),
      tag('base', attributes: ['href' => $baseHref . '/']),
      implode('', Map::execute(Codes::all(), 'description')),
    ];

    if ($favicon) {
      $head[] = tag('link', attributes: [
        'rel' => 'icon',
        'type' => 'image/' . $ext,
        'href' => $this->getView()->asset($favicon)
      ]);
    }

    return implode('', array_filter($head));
  }
}
