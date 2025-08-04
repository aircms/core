<?php

declare(strict_types=1);

namespace Air\Crud;

class Nav
{
  public static function item(
    string  $icon,
    string  $title,
    ?string $controller = null,
    ?array  $items = null,
    ?string $action = null,
    ?array  $params = null,
  ): array
  {
    $url = ['controller' => $controller];

    if ($action) {
      $url['action'] = $action;
    }

    if ($params) {
      $url['params'] = $params;
    }

    $nav = [
      'icon' => $icon,
      'title' => $title,
      'url' => $url
    ];

    if ($items) {
      $nav['items'] = $items;
    }

    return $nav;
  }

  public static function divider(): string
  {
    return 'divider';
  }
}