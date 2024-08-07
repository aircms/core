<?php

declare(strict_types=1);

/**
 * @param string|null $title
 * @param Closure|array|string|null $content
 * @param bool $opened
 * @param string|null $mainClass
 * @param string|null $titleClass
 * @param string|null $bodyClass
 * @return string
 */
function widget(
  string               $title = null,
  Closure|array|string $content = null,
  bool                 $opened = false,
  string               $mainClass = null,
  string               $titleClass = null,
  string               $bodyClass = null
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