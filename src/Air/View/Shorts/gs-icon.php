<?php

declare(strict_types=1);

function gsIcon(
  ?string           $icon = null,
  array             $attributes = [],
  string|array|null $class = null,
  int               $fill = 0,
  int               $weight = 300,
  int               $grad = 2,
  int               $opsz = 24,
  string            $tag = 'i'
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