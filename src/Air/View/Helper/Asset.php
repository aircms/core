<?php

declare(strict_types=1);

namespace Air\View\Helper;

use Air\Core\Exception\ClassWasNotFound;
use Air\Core\Front;

class Asset extends HelperAbstract
{
  /**
   * @var array|null
   */
  public static ?array $config = null;

  /**
   * @param string|array $assets
   * @param string|null $type
   * @return string
   * @throws ClassWasNotFound
   */
  public function call(string|array $assets, string $type = null): string
  {
    if (!self::$config) {
      self::$config = array_merge([
        'underscore' => false,
        'prefix' => '',
        'defer' => false,
        'async' => false
      ], Front::getInstance()->getConfig()['air']['asset'] ?? []);
    }

    $assetsHtml = [];
    $assetsType = $type;

    foreach ((array)$assets as $asset) {
      $assetParts = explode('.', explode('?', $asset)[0]);
      $type = $assetsType ?: $assetParts[count($assetParts) - 1];

      $assetsHtml[] = match ($type) {
        'js' => $this->js($asset),
        'ico', 'svg', 'jpg', 'jpeg', 'png', 'webp', 'json', 'woff', 'woff2' => $this->filter($asset),
        default => $this->css($asset),
      };
    }

    return implode("\n", $assetsHtml);
  }

  /**
   * @param string $uri
   * @return string
   */
  public function js(string $uri): string
  {
    return script(
      src: $this->filter($uri),
      defer: $this->config['defer'] ?? false,
      async: $this->config['async'] ?? false
    );
  }

  /**
   * @param string $uri
   * @return string
   */
  public function css(string $uri): string
  {
    return tag(tagName: 'link', attributes: [
      'href' => $this->filter($uri),
      'rel' => 'stylesheet'
    ]);
  }

  /**
   * @param string $uri
   * @return string
   */
  public function filter(string $uri): string
  {
    return $this->underscore($this->prefix($uri));
  }

  /**
   * @param string $uri
   * @return string
   */
  public function underscore(string $uri): string
  {
    if ($this->config['underscore'] ?? false) {

      if (str_contains($uri, '?')) {
        return $uri . '&_=' . microtime();
      }
      return $uri . '?_=' . microtime();
    }
    return $uri;
  }

  /**
   * @param string $uri
   * @return string
   */
  public function prefix(string $uri): string
  {
    if (!empty($this->config['prefix'])) {

      if (!str_starts_with($uri, '//') && !str_starts_with($uri, 'http://') && !str_starts_with($uri, 'https://')) {

        if (!str_starts_with($uri, '/')) {
          $uri = '/' . $uri;
        }

        $uri = $this->config['prefix'] . $uri;
      }
    }
    return $uri;
  }
}