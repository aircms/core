<?php

declare(strict_types=1);

/**
 * @param string $dataId
 * @param string|null $mainClass
 * @param string|null $slideClass
 * @param Closure|string|array|null $slides
 * @return string
 */
function swiper(
  string               $dataId,
  ?string              $mainClass = null,
  ?string              $slideClass = null,
  Closure|string|array $slides = null
): string
{
  return div(
    class: ['swiper', $mainClass],
    attributes: ['data-swiper-' . $dataId],
    content: function () use ($slideClass, $slides) {
      return div(
        class: 'swiper-wrapper',
        content: function () use ($slideClass, $slides) {
          foreach (content($slides) as $slide) {
            yield div(
              class: ['swiper-slide', $slideClass],
              content: $slide);
          }
        });
    });
}