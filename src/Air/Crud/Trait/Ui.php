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
    return span(content: (string)$label, class: ['badge', 'badge-' . $style]);
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
    return a(
      href: $url,
      content: $label,
      class: ['btn', 'btn-' . $style],
      attributes: $confirm ? ['data-confirm' => $confirm] : null
    );
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

    return a(
      href: $url,
      content: $label,
      class: ['text-decoration-underline', 'text-' . $style],
      attributes: $confirm ? ['data-confirm' => $confirm] : null
    );
  }

  /**
   * @param string $label
   * @param string $style
   * @return string
   */
  public static function label(string $label, string $style = 'primary'): string
  {
    return span(content: $label, class: 'text-' . $style);
  }

  /**
   * @param array $strings
   * @return string
   */
  public static function multiple(array $strings): string
  {
    return implode('<br>', array_filter($strings));
  }

  /**
   * @return string
   */
  public static function navBack(): string
  {
    return "<script>nav.back()</script>";
  }
}