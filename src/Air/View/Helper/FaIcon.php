<?php

declare(strict_types=1);

namespace Air\View\Helper;

class FaIcon extends HelperAbstract
{
  const string SOLID = 'solid';
  const string REGULAR = 'regular';
  const string LIGHT = 'light';
  const string THIN = 'thin';

  const string DUOTONE = 'duotone';
  const string SHARP = 'sharp';
  const string SHARP_DUOTONE = 'sharp-duotone';

  /**
   * @param string|null $icon
   * @param string|null $family
   * @param string|null $type
   * @param array $attributes
   * @param string $tag
   * @param string|null $class
   * @param string|null $style
   * @return string
   */
  public function call(
    string $icon = null,
    string $family = null,
    string $type = null,
    array   $attributes = [],
    string  $tag = 'i',
    string  $class = null,
    string  $style = null,
  ): string
  {
    if (!$icon) {
      return '';
    }

    $iconTag[] = '<' . $tag;

    $defaultClasses = ['fa-' . $icon];
    if ($family) {
      $defaultClasses[] = 'fa-' . $family;

    } else if (!$type) {
      $type = self::REGULAR;
    }

    if ($type) {
      $defaultClasses[] = 'fa-' . $type;
    }

    $attributes['class'] = implode(' ', [...$defaultClasses, $class ?? '']);

    if ($style) {
      $attributes['style'] = $style;
    }

    foreach ($attributes as $name => $value) {
      $iconTag[] = $name . '="' . $value . '"';
    }

    $iconTag[] = '></' . $tag . '>';

    return implode(' ', $iconTag);
  }
}
