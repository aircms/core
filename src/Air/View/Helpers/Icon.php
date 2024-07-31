<?php

declare(strict_types=1);

namespace Air\View\Helper;

class Icon extends HelperAbstract
{
  /**
   * @param string|null $icon
   * @param array $attributes
   * @param string $tag
   * @return string
   */
  public function call(?string $icon = null, array $attributes = [], string $tag = 'i'): string
  {
    if (!$icon) {
      return '';
    }

    $iconTag[] = '<' . $tag;

    $attributes['class'] = implode(' ', ['material-symbols-outlined', $attributes['class'] ?? '']);

    $fill = $attributes['fill'] ?? 0;
    unset($attributes['fill']);

    $weight = $attributes['weight'] ?? 300;
    unset($attributes['weight']);

    $grad = $attributes['grad'] ?? 2;
    unset($attributes['grad']);

    $opsz = $attributes['opsz'] ?? 24;
    unset($attributes['opsz']);

    $attributes['style'] = implode('; ', [
      "font-variation-settings: 'FILL' $fill, 'wght' $weight, 'GRAD' $grad, 'opsz' $opsz",
      $attributes['style'] ?? ''
    ]);

    foreach ($attributes as $name => $value) {
      $iconTag[] = $name . '="' . $value . '"';
    }

    $iconTag[] = '>' . $icon . '</' . $tag . '>';

    return implode(' ', $iconTag);
  }
}
