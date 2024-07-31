<?php

declare(strict_types=1);

namespace Air\View\Helper;

class Favicon extends HelperAbstract
{
  /**
   * @param string $asset
   * @return string
   */
  public function call(string $asset): string
  {
    $assetNameParts = explode('.', $asset);
    $ext = $assetNameParts[count($assetNameParts) - 1];

    return '<link rel="icon" type="image/' . $ext . '" href="' . $this->getView()->asset($asset) . '">';
  }
}
