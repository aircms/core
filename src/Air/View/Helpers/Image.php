<?php

declare(strict_types=1);

namespace Air\View\Helper;

use Air\Core\Exception\ClassWasNotFound;
use Air\Type\File;

class Image extends HelperAbstract
{
  /**
   * @param File|string|null $image
   * @param string|null $alt
   * @param string|null $title
   * @param string|null $class
   * @param array $attributes
   * @return string
   * @throws ClassWasNotFound
   */
  public function call(
    File|string|null $image = null,
    string           $alt = null,
    string           $title = null,
    string           $class = null,
    array            $attributes = [],
  ): string
  {
    $img[] = '<img';

    if ($image instanceof File) {
      if (!isset($attributes['src'])) {
        $attributes['src'] = $image->getSrc();
      }

      if (!isset($attributes['alt'])) {
        $attributes['alt'] = $image->getAlt();
      }

      if (!isset($attributes['title'])) {
        $attributes['title'] = $image->getTitle();
      }
    } elseif (is_string($image)) {
      if (str_starts_with($image, 'http://') && str_starts_with($image, 'https://')) {
        $attributes['src'] = $image;
      } else {
        $attributes['src'] = $this->getView()->asset($image);
      }
    }

    if ($class) {
      $attributes['class'] = $attributes['class'] ?? '';
      $attributes['class'] .= implode(' ', [
        $attributes['class'],
        $class
      ]);
    }

    if ($alt) {
      $attributes['alt'] = $alt;
    }

    if ($title) {
      $attributes['title'] = $title;
    }

    foreach ($attributes as $name => $value) {
      $img[] = $name . '="' . $value . '"';
    }

    $img[] = '/>';

    return implode(' ', $img);
  }
}
