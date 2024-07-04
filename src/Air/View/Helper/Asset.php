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
   * @var bool
   */
  public static bool $base = false;

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
        'svg', 'jpg', 'jpeg', 'png', 'webp', 'json' => $this->filter($asset),
        default => $this->css($asset),
      };
    }

    return implode("\n", $assetsHtml);
  }

  /**
   * @return string|void
   */
  public function base()
  {
    if (!self::$base) {
      self::$base = true;
      return '<base href="' . self::$config['prefix'] . '/"/>';
    }
  }

  /**
   * @param string $uri
   * @return string
   */
  public function js(string $uri): string
  {
    $defer = $this->config['defer'] ?? false ? 'defer' : '';
    $async = $this->config['async'] ?? false ? 'async' : '';

    $settings = implode(' ', [$defer, $async]);

    return $this->base() . '<script src="' . $this->filter($uri) . '" ' . $settings . '></script>';
  }

  /**
   * @param string $uri
   * @return string
   */
  public function css(string $uri): string
  {
    $defer = $this->config['defer'] ?? false ? 'defer' : '';
    $async = $this->config['async'] ?? false ? 'async' : '';

    $settings = implode(' ', [$defer, $async]);

    return $this->base() . '<link href="' . $this->filter($uri) . '" ' . $settings . ' rel="stylesheet" />';
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