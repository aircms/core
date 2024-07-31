<?php

declare(strict_types=1);

namespace Air\View\Helpers;

use Air\Core\Exception\ClassWasNotFound;
use Air\Core\Front;

class Asset
{
  /**
   * @var array|null
   */
  public static array|null $config = null;

  /**
   * @param string|array $assets
   * @param string|null $type
   * @return string
   * @throws ClassWasNotFound
   */
  public static function asset(string|array $assets, string $type = null): string
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
        'js' => self::js($asset),
        'svg', 'jpg', 'jpeg', 'png', 'webp', 'json', 'woff', 'woff2' => self::filter($asset),
        default => self::css($asset),
      };
    }

    return implode("\n", $assetsHtml);
  }

  /**
   * @param string $uri
   * @return string
   */
  public static function js(string $uri): string
  {
    global $config;

    $defer = $config['defer'] ?? false ? 'defer' : '';
    $async = $config['async'] ?? false ? 'async' : '';

    $settings = implode(' ', [$defer, $async]);


    return '<script src="' . self::filter($uri) . '" ' . $settings . '></script>';
  }

  /**
   * @param string $uri
   * @return string
   */
  public static function css(string $uri): string
  {
    global $config;

    $defer = $config['defer'] ?? false ? 'defer' : '';
    $async = $config['async'] ?? false ? 'async' : '';

    $settings = implode(' ', [$defer, $async]);

    return '<link href="' . self::filter($uri) . '" ' . $settings . ' rel="stylesheet" />';
  }

  /**
   * @param string $uri
   * @return string
   */
  public static function filter(string $uri): string
  {
    return self::underscore(self::prefix($uri));
  }

  /**
   * @param string $uri
   * @return string
   */
  public static function underscore(string $uri): string
  {
    if (self::$config['underscore'] ?? false) {

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
  public static function prefix(string $uri): string
  {
    if (!empty(self::$config['prefix'])) {

      if (!str_starts_with($uri, '//') && !str_starts_with($uri, 'http://') && !str_starts_with($uri, 'https://')) {

        if (!str_starts_with($uri, '/')) {
          $uri = '/' . $uri;
        }

        $uri = self::$config['prefix'] . $uri;
      }
    }
    return $uri;
  }
}