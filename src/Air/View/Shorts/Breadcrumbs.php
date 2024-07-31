<?php

declare(strict_types=1);

/**
 * @param string $mainClass
 * @param string $itemClass
 * @param string $itemClassActive
 * @param array $items
 * @return string
 */
function breadcrumbs(
  string $mainClass = 'breadcrumb',
  string $itemClass = 'breadcrumb-item',
  string $itemClassActive = 'active',
  array  $items = []
): string
{
  return ul(class: $mainClass, content: function () use ($itemClass, $itemClassActive, $items) {
    foreach ($items as $title => $href) {
      if (!is_int($title)) {
        yield li(class: $itemClass, content: a($href, content: $title));
      } else {
        yield li(class: [$itemClass, $itemClassActive], content: $href);
      }
    }
  });
}