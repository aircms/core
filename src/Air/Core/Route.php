<?php

declare(strict_types=1);

namespace Air\Core;

use Air\Crud\Model\Language;

class Route
{
  public static function r(
    ?string $context = null,
    ?string $controller = null,
    ?string $action = null,
    ?array  $params = null
  ): array
  {
    $route = [];

    if ($context) {
      $route['context'] = $context;
    }

    if ($controller) {
      $route['controller'] = $controller;
    }

    if ($action) {
      $route['action'] = $action;
    }

    if ($params) {
      $route['params'] = $params;
    }

    return $route;
  }

  public static function assembleRoute(
    ?string $context = null,
    ?string $controller = '',
    ?string $action = '',
    ?array  $params = [],
    ?bool   $onlyUrl = true
  ): string
  {
    return self::assembleCleanRoute(self::r($context, $controller, $action, $params), $onlyUrl);
  }

  public static function assembleCleanRoute(array $route, bool $onlyUri = true): string
  {
    $url = trim(Front::getInstance()->getRouter()->assemble($route, $route['params'] ?? [], true, $onlyUri));

    if (str_ends_with($url, '?')) {
      $url = substr($url, 0, strlen($url) - 1);
    }
    return $url;
  }

  public static function assemble(array $route = [], array $params = [], bool $onlyUri = true): string
  {
    if (!isset($params['language'])) {
      $language = Language::getLanguage();
      if ($language && !$language->isDefault) {
        $params['language'] = $language->key;
      }
    }

    $url = trim(Front::getInstance()->getRouter()->assemble($route, $params, true, $onlyUri));

    if (str_ends_with($url, '?')) {
      $url = substr($url, 0, strlen($url) - 1);
    }
    return $url;
  }

  public static function currentRoute(): array
  {
    $router = Front::getInstance()->getRouter();

    return [
      'route' => [
        'controller' => $router->getController(),
        'action' => $router->getAction(),
      ],
      'params' => [
        ...$router->getUrlParams(),
        ...$router->getRequest()->getParams()
      ]
    ];
  }

  public static function currentRouteWithParams(array $params = []): string
  {
    $currentRoute = self::currentRoute();

    return self::assemble($currentRoute['route'], [
      ...$currentRoute['params'],
      ...$params
    ]);
  }

  public static function getLanguageRouteWithPrefix(?Language $language = null): string
  {
    if (!$language) {
      $language = Language::getLanguage();
    }
    if ($language->isDefault) {
      return '';
    }
    return '/' . $language->key;
  }

  public static function currentRouteToLanguage(Language $language): string
  {
    $route = self::currentRoute();

    if ($language->isDefault) {
      unset($route['params']['language']);
    } else {
      $route['params']['language'] = $language->key;
    }

    return Front::getInstance()->getRouter()->assemble(
      $route['route'],
      $route['params'],
      true
    );
  }

  public static function assembleWithLanguage(
    Language $language,
    array    $route = [],
    array    $params = [],
    bool     $onlyUri = true,
  ): string
  {
    $params = $params ?? [];
    unset($params['language']);

    if (!$language->isDefault) {
      $params['language'] = $language->key;
    }

    $url = trim(Front::getInstance()->getRouter()->assemble($route, $params, true, $onlyUri));

    if (str_ends_with($url, '?')) {
      $url = substr($url, 0, strlen($url) - 1);
    }
    return $url;
  }
}