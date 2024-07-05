<?php

declare(strict_types=1);

namespace Air\Core;

use Air\Core\Exception\DomainMustBeProvided;
use Air\Core\Exception\RouterDomainWasNotFound;
use Air\Core\Exception\RouterWasNotFound;

/**
 * Class Router
 * @package Air
 */
class Router
{
  /**
   * @var Request|null
   */
  private ?Request $request = null;

  /**
   * @var string
   */
  private string $module = '';

  /**
   * @var string
   */
  private string $controller = '';

  /**
   * @var string
   */
  private string $action = '';

  /**
   * @var array
   */
  private array $routes = [];

  /**
   * @var array
   */
  private array $urlParams = [];

  /**
   * @var array
   */
  private array $injector = [];

  /**
   * @var array
   */
  private array $config = [];

  /**
   * @return string
   */
  public function getModule(): string
  {
    return $this->module;
  }

  /**
   * @param string $module
   */
  public function setModule(string $module): void
  {
    $this->module = $module;
  }

  /**
   * @return string
   */
  public function getController(): string
  {
    return $this->controller;
  }

  /**
   * @param string $controller
   */
  public function setController(string $controller)
  {
    $this->controller = $controller;
  }

  /**
   * @return string
   */
  public function getAction(): string
  {
    return $this->action;
  }

  /**
   * @param string $action
   */
  public function setAction(string $action): void
  {
    $this->action = $action;
  }

  /**
   * @return array
   */
  public function getRoutes(): array
  {
    return $this->routes;
  }

  /**
   * @param array $routes
   */
  public function setRoutes(array $routes): void
  {
    $this->routes = $routes;
  }

  /**
   * @return array
   */
  public function getUrlParams(): array
  {
    return $this->urlParams;
  }

  /**
   * @param array $urlParams
   */
  public function setUrlParams(array $urlParams): void
  {
    $this->urlParams = $urlParams;
  }

  /**
   * @return array
   */
  public function getInjector(): array
  {
    return $this->injector;
  }

  /**
   * @param array $injector
   */
  public function setInjector(array $injector): void
  {
    $this->injector = $injector;
  }

  /**
   * @return array
   */
  public function getConfig(): array
  {
    return $this->config;
  }

  /**
   * @param array $config
   */
  public function setConfig(array $config): void
  {
    $this->config = $config;
  }

  /**
   * @param array $requestedRoute
   * @param array $params
   * @param bool $reset
   * @return string
   * @throws DomainMustBeProvided
   */
  public function assemble(array $requestedRoute = [], array $params = [], bool $reset = false): string
  {
    $module = $requestedRoute['module'] ?? $this->module;
    $controller = $requestedRoute['controller'] ?? ($reset ? '' : $this->controller);
    $action = $requestedRoute['action'] ?? ($reset ? '' : $this->action);

    if (!$reset) {
      $params = array_merge($this->urlParams, $params);
    }

    $uri = null;
    $selectedDomain = null;

    foreach ($this->routes as $domain => $router) {

      if (($router['module'] ?? null) == $module) {

        $selectedDomain = $domain;

        if (str_contains($domain, '*')) {
          $selectedDomain = $this->getRequest()->getDomain();
        }

        foreach ($router['routes'] ?? [] as $routeUri => $route) {

          if ($routeUri == '*' && is_callable($route)) {
            continue;
          }

          $routeUri = ($router['prefix'] ?? '') . $routeUri;

          $currentRouteSettings = [
            'controller' => $route['controller'] ?? 'index',
            'action' => $route['action'] ?? 'index'
          ];

          $chController = $controller == '' ? 'index' : $controller;
          $chAction = $action == '' ? 'index' : $action;

          if ($chController == $currentRouteSettings['controller']
            && $chAction == $currentRouteSettings['action']) {

            $releaseParts = [];

            foreach (explode('/', $routeUri) as $part) {

              if (str_starts_with($part, ':')) {

                $var = substr($part, 1);

                if ($params[$var] ?? null) {
                  $releaseParts[] = $params[$var];
                  unset($params[$var]);
                }
              } else {
                $releaseParts[] = $part;
              }
            }

            $uri = implode('/', $releaseParts);
          }
        }
      }
    }

    if (!$selectedDomain) {
      throw new DomainMustBeProvided();
    }

    if (!$uri) {
      $uri = '/' . implode('/', array_filter([$controller, $action]));
    }

    if (count($params)) {
      $uri = $uri . '?' . http_build_query($params);
    }

    if ($selectedDomain == '*') {
      $selectedDomain = $this->getRequest()->getDomain();
    }

    $port = '';

    if ($this->getRequest()->getPort() != 80 && $this->getRequest()->getPort() != 443) {
      $port = ':' . $this->getRequest()->getPort();
    }

    return $this->getRequest()->getScheme() . '://' . $selectedDomain . $port . $uri;
  }

  /**
   * @return Request
   */
  public function getRequest(): Request
  {
    return $this->request;
  }

  /**
   * @param Request $request
   */
  public function setRequest(Request $request): void
  {
    $this->request = $request;
  }

  /**
   * @return void
   * @throws RouterDomainWasNotFound
   * @throws RouterWasNotFound
   */
  public function parse(): void
  {
    $domain = $this->getRequest()->getDomain();

    if (!isset($this->routes[$domain]) && count($this->routes)) {

      $isSet = false;
      foreach ($this->routes as $route => $settings) {
        if (str_contains($domain, str_replace('*', '', $route))) {
          $domain = $route;
          $isSet = true;
          break;
        }
      }

      if (!$isSet && !isset($this->routes['*'])) {
        throw new RouterDomainWasNotFound($domain);
      }

    } else if (!isset($this->routes[$domain])) {
      $domain = '*';
    }

    $this->setConfig($this->routes[$domain]['air'] ?? []);

    $routes = $this->routes[$domain] ?? [];
    $this->module = $routes['module'] ?? '';

    $uri = explode('?', $this->getRequest()->getUri())[0];

    $parts = array_values(array_filter(explode('/', $uri)));

    if (!isset($routes['routes'])) {

      if (($routes['strict'] ?? false) === true) {
        throw new RouterWasNotFound($uri);
      }

      $this->controller = $parts[0] ?? 'index';
      $this->action = $parts[1] ?? 'index';

      return;
    }

    $prefix = $routes['prefix'] ?? '';

    if ($uri != '/' && str_ends_with($uri, '/')) {
      $uri = $uri . '/';
    }

    $match = false;
    $withPrefix = false;

    foreach ($routes['routes'] as $routerUri => $settings) {

      if (str_ends_with($uri, '//') && $routerUri != '/') {
        $routerUri = $routerUri . '/';
      }

      $pattern = preg_replace('/\\\:[А-Яа-яЁёa-zA-Z0-9\_\-]+/', '([А-Яа-яЁёa-zA-Z0-9\-\_]+)', preg_quote($routerUri, '@'));
      $pattern = "@^$pattern/?$@uD";

      $matches = [];

      if (preg_match($pattern, $uri, $matches)) {
        $match = true;
        break;
      }
    }

    if (!$match) {

      $withPrefix = true;

      foreach ($routes['routes'] as $routerUri => $settings) {

        if (str_ends_with($uri, '//') && $routerUri != '/') {
          $routerUri = $routerUri . '/';
        }

        $patternPrefix = preg_replace(
          '/\\\:[А-Яа-яЁёa-zA-Z0-9\_\-]+/',
          '([А-Яа-яЁёa-zA-Z0-9\-\_]+)',
          preg_quote($prefix . $routerUri, '@')
        );

        $patternPrefix = "@^$patternPrefix/?$@uD";

        if (preg_match($patternPrefix, $uri, $matches)) {
          $match = true;
          break;
        }
      }
    }

    if ($match) {

      array_shift($matches);

      $this->controller = $settings['controller'] ?? 'index';
      $this->action = $settings['action'] ?? 'index';
      $this->urlParams = $settings['params'] ?? [];

      $paramIndex = 0;

      if ($withPrefix) {
        $routerUri = $prefix . $routerUri;
      }

      foreach (explode('/', $routerUri) as $routerUriPart) {

        if (str_starts_with($routerUriPart, ':')) {

          $this->getRequest()->setGetParam(
            substr($routerUriPart, 1),
            $matches[$paramIndex] ?? null
          );

          $this->urlParams[substr($routerUriPart, 1)] = $matches[$paramIndex] ?? null;

          $paramIndex++;
        }
      }

      $this->injector = array_merge($settings['injector'] ?? [], $routes['prefixInjector'] ?? []);

      foreach ($this->urlParams as $key => $value) {
        $this->getRequest()->setGetParam($key, $value);
      }

      return;
    }

    if (isset($routes['routes']['*']) && is_callable($routes['routes']['*'])) {

      $route = $routes['routes']['*']($uri);

      if (is_array($route)) {

        $this->controller = $route['controller'] ?? 'index';
        $this->action = $route['action'] ?? 'index';
        $this->urlParams = $route['params'] ?? [];
        $this->injector = $route['injector'] ?? [];

        foreach ($this->urlParams as $key => $value) {
          $this->getRequest()->setGetParam($key, $value);
        }

        return;
      }
    }

    if (
      isset($routes['strict'])
      && $routes['strict'] === true
      && $parts[0] !== 'robots.txt'
      && !($parts[0] === 'fonts' && $parts[1] === 'css')
    ) {
      throw new RouterWasNotFound($uri);
    }

    if ($parts[0] === 'fonts' && $parts[1] === 'css') {
      $this->controller = 'fonts';
      $this->action = 'index';

    } else {
      $this->controller = $parts[0] ?? 'index';
      $this->action = $parts[1] ?? 'index';
    }

    $parts = array_slice($parts, 2);

    for ($i = 0; $i < count($parts); $i += 2) {

      if (isset($parts[$i + 1])) {
        $this->getRequest()->setGetParam($parts[$i], $parts[$i + 1]);
        $this->urlParams[$parts[$i]] = $parts[$i + 1];
      }
    }
  }
}
