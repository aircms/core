<?php

declare(strict_types=1);

function widget(
  ?string                   $title = null,
  Closure|array|string|null $content = null,
  bool                      $opened = false,
  ?string                   $mainClass = null,
  ?string                   $titleClass = null,
  ?string                   $bodyClass = null
): string
{
  return div(
    class: ['widget', $mainClass, !$opened ?: 'show'],
    content: [
      div(class: ['title', $titleClass], content: $title),
      div(class: ['body', $bodyClass], content: content($content)),
    ]
  );
}