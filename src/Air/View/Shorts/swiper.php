<?php

declare(strict_types=1);

/**
 * @param string $dataId
 * @param mixed $slides
 * @param Closure $renderer
 * @param Closure|string|array|null $additionalContent
 * @param string|null $mainClass
 * @param string|null $slideClass
 * @return string
 */
function swiper(
  string  $dataId,
  mixed   $slides,
  Closure $renderer,
  Closure|string|array|null $additionalContent = null,
  ?string $mainClass = null,
  ?string $slideClass = null,
): string
{
  $content = [];
  foreach ($slides as $index => $slide) {
    ob_start();
    $value = $renderer($slide, $index);
    if (!$value) {
      $value = ob_get_contents();
    }
    ob_end_clean();
    $content[] = div(['swiper-slide', $slideClass], $value);
  }

  $additionalContent = implode('', content($additionalContent));

  return div(
    class: ['swiper', $mainClass],
    attributes: ['data-swiper-' . $dataId],
    content: [
      div(class: 'swiper-wrapper', content: $content),
      $additionalContent,
    ]
  );
}