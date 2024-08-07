<?php

declare(strict_types=1);

use Air\Type\File;

function container(
  Closure|string|array|null $content = null,
  string|array              $class = null,
  array                     $attributes = [],
  File|string               $bgImage = null
): string
{
  $class = (array)$class ?? [];
  $class[] = 'container';

  return div(
    content: $content,
    class: $class,
    attributes: $attributes,
    bgImage: $bgImage
  );
}

function flex(
  string|array              $class = null,
  Closure|string|array|null $content = null,
  array                     $attributes = [],
  File|string               $bgImage = null
): string
{
  $class = (array)$class ?? [];
  $class[] = 'd-f';

  return div(
    content: $content,
    class: $class,
    attributes: $attributes,
    bgImage: $bgImage
  );
}

function row(
  Closure|string|array|null $content = null,
  string|array              $class = null,
  array                     $attributes = [],
  File|string               $bgImage = null
): string
{
  $class = (array)$class ?? [];
  $class[] = 'row';

  return div(
    content: $content,
    class: $class,
    attributes: $attributes,
    bgImage: $bgImage
  );
}

function col(
  string|array              $class = null,
  Closure|string|array|null $content = null,
  array                     $attributes = [],
  File|string               $bgImage = null
): string
{
//  $class = (array)$class ?? [];
//  $class[] = 'col';

  return div(
    content: $content,
    class: $class,
    attributes: $attributes,
    bgImage: $bgImage
  );
}