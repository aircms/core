<?php

declare(strict_types=1);

namespace Air\View\Helper;

use Air\Core\Exception\ClassWasNotFound;
use Air\Type\File;

class Image extends HelperAbstract
{
  /**
   * @param File $image
   * @param array $attributes
   * @return string
   * @throws ClassWasNotFound
   */
  public function call(File $image, array $attributes = []): string
  {
    $img[] = '<img';

    if (!isset($attributes['src'])) {
      $attributes['src'] = $image->getSrc();
    }

    if (!isset($attributes['alt'])) {
      $attributes['alt'] = $image->getAlt();
    }

    if (!isset($attributes['title'])) {
      $attributes['title'] = $image->getTitle();
    }

    foreach ($attributes as $name => $value) {
      $img[] = $name . '="' .  $value. '"';
    }

    $img[] = '/>';

    return implode(' ', $img);
  }
}
