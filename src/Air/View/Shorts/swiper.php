<?php

declare(strict_types=1);

function swiper(
  mixed                     $slides,
  Closure                   $renderer,
  string                    $dataId = null,
  Closure|string|array|null $additionalContent = null,
  ?string                   $mainClass = null,
  ?string                   $slideClass = null,
  bool                      $slideInsideValue = false,
  array                     $options = []
): string
{
  if (count($options)) {
    if (!isset($options['spaceBetween'])) {
      $options['spaceBetween'] = 20;
    }
    if (!isset($options['slidesPerView'])) {
      $options['slidesPerView'] = 1;
    }
  }

  $content = [];
  foreach ($slides as $index => $slide) {
    ob_start();
    $value = $renderer($slide, $index);
    if (!$value) {
      $value = ob_get_contents();
    }
    ob_end_clean();

    if ($slideInsideValue) {
      $content[] = $value;
    } else {
      $content[] = div(['swiper-slide', $slideClass], $value);
    }
  }

  $additionalContent = implode('', content($additionalContent));

  $data = ['swiper'];

  if ($dataId) {
    $data[] = 'swiper-' . $dataId;
  }

  if (count($options)) {
    $data['swiper-options'] = str_replace('"', "'", json_encode($options));
  }

  $swiper = div(
    class: ['swiper', $mainClass],
    data: $data,
    content: [
      div(class: 'swiper-wrapper', content: $content),
      $additionalContent,
    ]
  );

  if (strlen($additionalContent)) {
    return div(data: 'swiper-container', content: $swiper);
  }

  return $swiper;
}

function swiperStatic(
  array                     $slides,
  string                    $dataId = null,
  Closure|string|array|null $additionalContent = null,
  ?string                   $mainClass = null,
  ?string                   $slideClass = null,
  bool                      $slideInsideValue = false,
  array                     $options = []
): string
{
  if (count($options)) {
    if (!isset($options['spaceBetween'])) {
      $options['spaceBetween'] = 20;
    }
    if (!isset($options['slidesPerView'])) {
      $options['slidesPerView'] = 1;
    }
  }

  $content = [];
  foreach ($slides as $slide) {
    $value = content($slide);

    if ($slideInsideValue) {
      $content[] = $value;
    } else {
      $content[] = div(['swiper-slide', $slideClass], $value);
    }
  }

  $additionalContent = implode('', content($additionalContent));

  $data = ['swiper'];

  if ($dataId) {
    $data[] = 'swiper-' . $dataId;
  }

  if (count($options)) {
    $data['swiper-options'] = str_replace('"', "'", json_encode($options));
  }

  $swiper = div(
    class: ['swiper', $mainClass],
    data: $data,
    content: [
      div(class: 'swiper-wrapper', content: $content),
      $additionalContent,
    ]
  );

  if (strlen($additionalContent)) {
    return div(data: 'swiper-container', content: $swiper);
  }

  return $swiper;
}