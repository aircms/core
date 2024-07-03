<?php

declare(strict_types=1);

namespace Air\Crud\Trait;

trait Ui
{
  const string PRIMARY = 'primary';
  const string SECONDARY = 'secondary';
  const string SUCCESS = 'success';
  const string INFO = 'info';
  const string WARNING = 'warning';
  const string DANGER = 'danger';
  const string LIGHT = 'light';
  const string DARK = 'dark';

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

  /**
   * @param string $label
   * @param string $url
   * @param string|null $confirm
   * @param string $style
   * @return string
   */
  public static function link(string $label, string $url, ?string $confirm = null, string $style = 'primary'): string
  {
    if (str_contains($url, '@')) {
      $url = 'mailto:' . $url;

    } else if (!str_starts_with($url, 'http')) {
      $url = 'tel:' . $url;
    }

    $html = '<a class="text-decoration-underline text-' . $style . '" href="' . $url . '"';

    if ($confirm) {
      $html .= ' data-confirm="' . $confirm . '"';
    }

    return $html . '>' . $label . '</a>';
  }

  /**
   * @param string $label
   * @param string $url
   * @param string|null $confirm
   * @param string $style
   * @return string
   */
  public static function label(string $label, string $style = 'primary'): string
  {
    return '<span class="text-' . $style . '">' . $label . '</a>';
  }

  /**
   * @param array $strings
   * @return string
   */
  public static function multiple(array $strings): string
  {
    return implode('<br>', $strings);
  }

  /**
   * @return string
   */
  public static function navBack(): string
  {
    return "<script>nav.back()</script>";
  }
}