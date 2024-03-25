<?php

declare(strict_types=1);

namespace Air\Core;

use Air\View\View;

class Controller
{
  /**
   * @var Request|null
   */
  private ?Request $request = null;

  /**
   * @var Response|null
   */
  private ?Response $response = null;

  /**
   * @var Router|null
   */
  private ?Router $router = null;

  /**
   * @var View|null
   */
  private ?View $view = null;

  /**
   * @return Response|null
   */
  public function getResponse(): ?Response
  {
    return $this->response;
  }

  /**
   * @param Response $response
   * @return void
   */
  public function setResponse(Response $response): void
  {
    $this->response = $response;
  }

  /**
   * @return Router|null
   */
  public function getRouter(): ?Router
  {
    return $this->router;
  }

  /**
   * @param Router $router
   * @return void
   */
  public function setRouter(Router $router): void
  {
    $this->router = $router;
  }

  /**
   * @return View
   */
  public function getView(): View
  {
    return $this->view;
  }

  /**
   * @param View $view
   */
  public function setView(View $view): void
  {
    $this->view = $view;
  }

  /**
   * @param string $uri
   */
  public function redirect(string $uri): void
  {
    header('Location: ' . $uri);
    die();
  }

  /**
   * @param string $name
   * @param null $default
   * @param array $filters
   *
   * @return array|int|mixed|string|null
   */
  public function getParam(string $name, $default = null, array $filters = []): mixed
  {
    return $this->getRequest()->getParam($name, $default, $filters) ??
      $this->getRequest()->getGet($name, $default, $filters);
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
   * @return array
   */
  public function getParams(): array
  {
    return array_merge(
      $this->getRequest()->getParams(),
      $this->getRequest()->getGetAll()
    );
  }

  public function init(): void
  {
  }

  public function postRun(): void
  {
  }
}
