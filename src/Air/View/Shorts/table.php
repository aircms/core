<?php

declare(strict_types=1);

function table(
  Closure|string|array|null|Generator $trs = null,
  string|array                        $class = null,
  array|string                        $attributes = [],
  array|string                        $data = [],
)
{
  return tag(
    tagName: 'table',
    content: $trs,
    class: $class,
    attributes: $attributes,
    data: $data,
  );
}

function tr(
  Closure|string|array|null|Generator $tds = [],
  string|array                        $class = null,
  array|string                        $attributes = [],
  array|string                        $data = [],
)
{
  return tag(
    tagName: 'tr',
    content: $tds,
    class: $class,
    attributes: $attributes,
    data: $data,
  );
}

function td(
  Closure|string|array|null $content = null,
  string|array              $class = null,
  array|string              $attributes = [],
  array|string              $data = [],
)
{
  return tag(
    tagName: 'td',
    content: $content,
    class: $class,
    attributes: $attributes,
    data: $data,
  );
}