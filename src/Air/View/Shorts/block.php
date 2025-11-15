<?php

declare(strict_types=1);

use Air\Type\File;

function container(
  Closure|string|array|null $content = null,
  string|array|null         $class = null,
  array|string              $attributes = [],
  array|string              $data = [],
  File|string|null          $bgImage = null
): string
{
  $class = (array)$class ?? [];
  $class[] = 'container';

  return div(
    content: $content,
    class: $class,
    attributes: $attributes,
    data: $data,
    bgImage: $bgImage
  );
}

function flex(
  string|array|null         $class = null,
  Closure|string|array|null $content = null,
  array|string              $attributes = [],
  array|string              $data = [],
  File|string               $bgImage = null
): string
{
  $class = (array)$class ?? [];
  $class[] = 'd-f';

  return div(
    content: $content,
    class: $class,
    attributes: $attributes,
    data: $data,
    bgImage: $bgImage
  );
}

function row(
  Closure|string|array|null $content = null,
  string|array|null         $class = null,
  array                     $attributes = [],
  File|string|null          $bgImage = null
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
  string|array              $class = [],
  Closure|string|array|null $content = null,
  array                     $attributes = [],
  File|string               $bgImage = null
): string
{
  $class = (array)$class ?? [];
  $class[] = 'col';

  return div(
    content: $content,
    class: $class,
    attributes: $attributes,
    bgImage: $bgImage
  );
}