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
   * @var array
   */
  public static array $css = [];

  /**
   * @var array
   */
  public static array $js = [];

  /**
   * @var array
   */
  public static array $thirdPartyCss = [];

  /**
   * @var array
   */
  public static array $thirdPartyJs = [];

  /**
   * @return self
   * @throws ClassWasNotFound
   */
  public function call(): self
  {
    if (!self::$config) {
      self::$config = array_merge(
        [
          'minify' => false,
          'underscore' => false,
          'prefix' => '',
        ],
        Front::getInstance()->getConfig()['air']['asset'] ?? []
      );
    }

    return $this;
  }

  /**
   * @return string
   * @throws ClassWasNotFound
   */
  public function base(): string
  {
    return '<base href="' . self::$config['prefix'] . '/">';
  }

  /**
   * @param array|string|null $assets
   * @return self
   */
  public function css(array|string $assets = null): self
  {
    foreach ((array)$assets as $asset) {
      if (str_starts_with($asset, 'http')) {
        self::$thirdPartyCss[$asset] = $asset;
        continue;
      }
      self::$css[$asset] = $this->filter($asset);
    }
    return $this;
  }

  /**
   * @param array|string $asset
   * @param bool $defer
   * @param bool $async
   * @return self
   */
  public function js(array|string $assets, bool $defer = false, bool $async = false): self
  {
    foreach ((array)$assets as $asset) {
      if (str_starts_with($asset, 'http')) {
        self::$thirdPartyJs[$asset] = $asset;
        continue;
      }
      self::$js[$asset] = $this->filter($asset);
    }
    return $this;
  }

  /**
   * @return string
   */
  public function toCss(): string
  {
    $rendered = null;
    foreach (self::$thirdPartyCss as $css) {
      $rendered .= '<link href="' . $css . '" rel="stylesheet" />';
    }

    if (!self::$config['minify']) {
      foreach (self::$css as $css) {
        $rendered .= '<link href="' . $css . '" rel="stylesheet" />';
      }
    } else {
      $css = base64_encode(implode('|', array_keys(self::$css)));
      $rendered .= '<link href="/' . self::$config['minify'] . '/css?s=' . $css . '" rel="stylesheet" />';
    }

    return $rendered;
  }

  /**
   * @return string
   */
  public function toJs(): string
  {
    $rendered = null;

    foreach (self::$thirdPartyJs as $js) {
      $rendered .= '<script src="' . $js . '" defer></script>';
    }

    if (!self::$config['minify']) {
      foreach (self::$css as $css) {
        $rendered .= '<link href="' . $css . '" rel="stylesheet" />';
      }
    } else {
      $css = base64_encode(implode('|', array_keys(self::$js)));
      $rendered .= '<script src="/' . self::$config['minify'] . '/js?s=' . $css . '" defer></script>';
    }

    return $rendered;
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