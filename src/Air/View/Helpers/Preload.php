<?php

declare(strict_types=1);

namespace Air\View\Helper;

class Preload extends HelperAbstract
{
  /**
   * @param string|array $assets
   * @param array $attributes
   * @return string
   */
  public function call(string|array $assets, array $attributes = []): string
  {
    $attributes = array_merge(['rel' => 'preload', 'crossorigin' => 'anonymous'], $attributes);

    $assetsHtml = [];
    foreach ((array)$assets as $asset) {
      $assetAttributes = $this->computeAttributes(array_merge([
        'href' => $asset,
        'as' => $this->getAs($asset),
        'type' => $this->getMine($asset)
      ], $attributes));

      $assetsHtml[] = '<link ' . $assetAttributes . '/>';
    }

    return implode('', $assetsHtml);
  }

  /**
   * @param array $attributes
   * @return string
   */
  private function computeAttributes(array $attributes): string
  {
    $attributesHtml = [];
    foreach ($attributes as $name => $value) {
      $attributesHtml[] = $name . '="' . $value . '"';
    }
    return implode(' ', $attributesHtml);
  }

  /**
   * @param string $asset
   * @return string
   */
  private function getAs(string $asset): string
  {
    return match ($this->getAssetExtension($asset)) {
      'woff', 'woff2' => 'font'
    };
  }

  /**
   * @param string $asset
   * @return string
   */
  private function getMine(string $asset): string
  {
    return match ($this->getAssetExtension($asset)) {
      'woff' => 'font/woff',
      'woff2' => 'font/woff2'
    };
  }

  /**
   * @param string $asset
   * @return string
   */
  private function getAssetExtension(string $asset): string
  {
    $asset = explode('?', $asset)[0];
    $asset = explode('.', $asset);

    return $asset[array_key_last($asset)];
  }
}
