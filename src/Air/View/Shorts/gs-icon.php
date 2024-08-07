<?php

declare(strict_types=1);

use Air\Core\Exception\ClassWasNotFound;

/**
 * @param string|null $icon
 * @param array $attributes
 * @param string|array|null $class
 * @param int $fill
 * @param int $weight
 * @param int $grad
 * @param int $opsz
 * @param string $tag
 * @return string
 * @throws ClassWasNotFound
 */
function gsIcon(
  string       $icon = null,
  array        $attributes = [],
  string|array $class = null,
  int          $fill = 0,
  int          $weight = 300,
  int          $grad = 2,
  int          $opsz = 24,
  string       $tag = 'i'
): string
{
  $attributes = $attributes ?? [];
  $attributes['style'] = "font-variation-settings: 'FILL' $fill, 'wght' $weight, 'GRAD' $grad, 'opsz' $opsz";

  return tag(
    tagName: $tag,
    attributes: $attributes,
    class: $class,
    content: $icon
  );
}