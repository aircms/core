<?php

declare(strict_types=1);

use Air\Type\FaIcon;

function faIcon(
  string|FaIcon $icon = null,
  string        $style = null,
  string|array  $data = [],
  array         $attributes = [],
  string        $tag = 'i',
  string|array  $class = null,
): string
{
  if ($icon === null) {
    return '';
  }

  if (is_string($icon)) {
    $icon = new FaIcon([
      'icon' => $icon,
    ]);
  }

  $class = (array)$class ?? [];

  if ($icon->isBrand()) {
    $class[] = 'fa-brands';

  } else {
    $class[] = $style ?? $icon->getStyle();
  }

  $class[] = 'fa-' . $icon->getIcon();

  $class = array_unique($class);

  return tag(
    tagName: $tag,
    class: $class,
    data: $data,
    attributes: $attributes,
  );
}