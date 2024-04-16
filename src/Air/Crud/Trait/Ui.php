<?php

declare(strict_types=1);

namespace Air\Crud\Trait;

trait Ui
{
  /**
   * @param mixed $label
   * @param string $style
   * @return string
   */
  public static function badge(mixed $label, string $style = 'primary'): string
  {
    return '<span class="badge badge-' . $style . '">' . $label . '</span>';
  }

  /**
   * @param array $badges
   * @return string
   */
  public static function badges(mixed $badges): string
  {
    $html = [];
    foreach ($badges as $badge) {
      $html[] = self::badge($badge['label'], $badge['style'] ?? 'primary');
    }
    return implode('<br>', $html);
  }

  /**
   * @param string $label
   * @param string $url
   * @param string|null $confirm
   * @param string $style
   * @return string
   */
  public static function btn(string $label, string $url, ?string $confirm = null, string $style = 'primary'): string
  {
    $html = '<a class="btn btn-' . $style . '" href="' . $url . '"';

    if ($confirm) {
      $html .= ' data-confirm="' . $confirm . '"';
    }

    return $html . '>' . $label . '</a>';
  }
}