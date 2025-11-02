<?php

declare(strict_types=1);

namespace Air\Crud\Controller\MultipleHelper\Accessor;

use Air\Model\ModelAbstract;
use Air\Type\File;
use Closure;

class Ui
{
  const string PRIMARY = 'primary';
  const string SECONDARY = 'secondary';
  const string SUCCESS = 'success';
  const string INFO = 'info';
  const string WARNING = 'warning';
  const string DANGER = 'danger';
  const string LIGHT = 'light';
  const string DARK = 'dark';

  public static function duration(
    int  $seconds,
    bool $showDays = true,
    bool $showHours = true,
    bool $showMinutes = true,
    bool $showSeconds = true
  ): string
  {
    $parts = [];

    $days = intdiv($seconds, 86400);
    $seconds %= 86400;
    $hours = intdiv($seconds, 3600);
    $seconds %= 3600;
    $minutes = intdiv($seconds, 60);
    $seconds %= 60;

    if ($days > 0 && $showDays) {
      $parts[] = "$days day";
    }
    if ($hours > 0 && $showHours) {
      $parts[] = "$hours hour";
    }
    if ($minutes > 0 && $showMinutes) {
      $parts[] = "$minutes minute";
    }
    if (($seconds > 0 || empty($parts)) && $showSeconds) {
      $parts[] = "$seconds second";
    }

    return implode(' ', $parts);
  }

  public static function date(int $timestamp, string $format = 'Y/m/d H:i'): string
  {
    return date($format, $timestamp);
  }

  public static function badge(mixed $label, ?string $style = Ui::PRIMARY): string
  {
    return span(content: (string)$label, class: ['badge', 'badge-' . $style]);
  }

  public static function btn(
    string  $label,
    string  $url,
    ?string $confirm = null,
    string  $style = Ui::PRIMARY,
    ?string $icon = null,
    array   $attributes = [],
  ): string
  {
    return a(
      href: $url,
      content: [
        $icon ? faIcon($icon, class: 'me-2') : null,
        $label,
      ],
      class: ['btn', 'btn-' . $style],
      attributes: [
        ...count($attributes) ? $attributes : [],
        ...$confirm ? ['data-confirm' => $confirm] : []
      ]
    );
  }

  public static function link(
    string  $label,
    string  $url,
    ?string $confirm = null,
    string  $style = 'primary',
    bool    $isBlank = false
  ): string
  {
    if (str_contains($url, '@')) {
      $url = 'mailto:' . $url;

    } else if (!str_starts_with($url, 'http')) {
      $url = 'tel:' . $url;
    }

    $attributes = [];

    if ($confirm) {
      $attributes['data-confirm'] = $confirm;
    }

    if ($isBlank) {
      $attributes['target'] = '_blank';
    }

    return a(
      href: $url,
      content: $label,
      class: ['text-decoration-underline', 'text-' . $style],
      attributes: $attributes
    );
  }

  public static function modelPreviewInline(ModelAbstract $model): string
  {
    return div(class: '', content: div(
      class: 'd-flex align-items-center',
      content: [
        $model->getMeta()->hasProperty('image') ? div(class: 'bg-image', content: self::imgPreview($model->image, 'w-auto')) : null,
        span($model->title)
      ]
    ));
  }

  public static function modelPreview(ModelAbstract $model): string
  {
    return div(class: 'admin-table-row-image', content: div(
      class: 'd-flex align-items-center',
      content: [
        $model->getMeta()->hasProperty('image') ? div(class: 'bg-image me-3', content: self::imgPreview($model->image)) : null,
        span($model->title)
      ]
    ));
  }

  public static function imgsPreview(?array $files = null): string
  {
    $previews = [];
    foreach (($files ?? []) as $file) {
      $previews[] = self::imgPreview($file, 'w-auto');
    }
    return div(class: 'd-flex', content: Ui::multipleLine($previews));
  }

  public static function imgPreview(File|string $image, ?string $class = null): string
  {
    $alt = '';
    $title = '';
    $src = $image;
    $mime = '';
    $thumbnail = $image;

    if ($image instanceof File) {
      $alt = $image->getAlt();
      $title = $image->getTitle();
      $src = $image->getSrc();
      $mime = $image->getMime();
      $thumbnail = $image->getThumbnail();
    }

    return div(
      class: 'admin-table-row-image ' . $class,
      content: div(
        class: 'bg-image rounded-4 shadow-5-strong me-3',
        attributes: ['role' => 'button'],
        data: [
          'admin-embed-modal',
          'admin-embed-modal-alt' => $alt,
          'admin-embed-modal-title' => $title,
          'admin-embed-modal-src' => $src,
          'admin-embed-modal-mime' => $mime,
          'admin-async-image' => $thumbnail,
          'mdb-ripple-init'
        ]
      )
    );
  }

  public static function label(string $label, string $style = 'primary'): string
  {
    return span(content: $label, class: 'text-' . $style);
  }

  public static function multiple(array $strings, string $separator = '<br>'): string
  {
    return implode($separator, array_filter($strings));
  }

  public static function multipleLine(array $strings, string $separator = ' '): string
  {
    return implode($separator, array_filter($strings));
  }

  public static function navBack(): string
  {
    return "<script>nav.back()</script>";
  }

  public static function modal(string $url, Closure|string|array|null $content): string
  {
    return div(content: render(content($content)), attributes: [
      'onclick' => "modal.embed('" . $url . "');",
      'role' => 'button'
    ]);
  }

  public static function header(Closure|string|array|null $title, ?string $icon = null, ?array $buttons = []): string
  {
    return div(
      class: 'p-3 position-sticky top-0 z-i-1001 header',
      content: div(
        class: 'card position-sticky w-100',
        content: div(
          class: 'card-body p-3 d-flex justify-content-between align-items-center',
          content: [
            h5(class: 'm-0 p-0', content: [
              $icon ? faIcon($icon, class: 'me-2') : null,
              self::content($title),
            ]),
            div(
              class: 'd-flex align-items-center gap-2',
              content: $buttons
            )
          ]
        )
      )
    );
  }

  public static function properties(array $keyValues = []): string
  {
    return row(
      class: 'gy-2',
      content: function () use ($keyValues) {
        foreach (array_filter($keyValues) as $key => $value) {
          $collClass = 'col-12';
          if (!is_int($key)) {
            $collClass = 'col-6';
            yield col($collClass . ' small', render(content($key)));
          }
          yield col($collClass, render(content((string)$value)));
        }
      }
    );
  }

  public static function content(Closure|string|array|null $content): string
  {
    return div(class: 'p-3 pt-0 overflow-hidden overflow-y-auto z-1 content', content: $content);
  }

  public static function card(
    Closure|string|array|null $header1 = null,
    Closure|string|array|null $header2 = null,
    Closure|string|array|null $content = null,
    string|array|null              $containerClass = null,
  ): string
  {
    return div(
      class: ['card', $containerClass],
      content: div(
        class: 'card-body p-3',
        content: [
          div(class: 'row', content: [
            check($header1, div(class: 'col-6', content: h6(class: 'p-0 m-0 lead d-flex align-items-center gap-2', content: $header1))),
            check($header2, div(class: 'col-6 text-end', content: $header2))
          ]),
          check($header1 || $header2, hr()),
          check($content, div(content: $content))
        ]
      )
    );
  }
}
