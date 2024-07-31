<?php

declare(strict_types=1);

namespace Air\Core;

use Air\Crud\Controller\Asset;
use Air\Crud\Controller\Codes;
use Air\Crud\Controller\FontsUi;
use Air\Crud\Controller\Language;
use Air\Crud\Controller\Phrase;
use Air\Crud\Controller\RobotsTxt;
use Air\Crud\Controller\RobotsTxtUi;
use Error;
use Exception;
use Throwable;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;

use Air\Crud\Controller\Admin;
use Air\Crud\Controller\Font;
use Air\Crud\Controller\History;
use Air\Crud\Controller\Log;
use Air\Crud\Controller\Login;
use Air\Crud\Controller\Storage;
use Air\Crud\Controller\System;
use Air\Core\Exception\ActionMethodIsReserved;
use Air\Core\Exception\ClassWasNotFound;
use Air\Core\Exception\Stop;
use Air\Model\ModelAbstract;
use Air\View\View;

final class Front
{
  /**
   * @var Front|null
   */
  private static ?Front $_instance = null;

  /**
   * @var array
   */
  private array $config;

  /**
   * @var Loader|null
   */
  private ?Loader $loader;

  /**
   * @var BootstrapAbstract
   */
  private mixed $bootstrap = null;

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
  private ?View $_view = null;

  /**
   * @param array $config
   * @throws ClassWasNotFound
   */
  private function __construct(array $config = [])
  {
    $this->config = $config;
    $this->loader = new Loader($this->config);

    $bootstrapClassName = '\\' . $this->config['air']['loader']['namespace'] . '\\Bootstrap';

    if (class_exists($bootstrapClassName)) {
      $this->bootstrap = new $bootstrapClassName();
      $this->bootstrap->setConfig($this->config);
    }
  }

  /**
   * @param array $config
   * @return Front
   * @throws ClassWasNotFound
   */
  public static function getInstance(array $config = []): Front
  {
    if (!self::$_instance) {
      self::$_instance = new self($config);
    }

    return self::$_instance;
  }

  /**
   * @return Router
   */
  public function getRouter(): Router
  {
    return $this->router;
  }

  /**
   * @param Router $router
   */
  public function setRouter(Router $router): void
  {
    $this->router = $router;
  }

  /**
   * @return Response
   */
  public function getResponse(): Response
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
   * @return Request
   */
  public function getRequest(): Request
  {
    return $this->request;
  }

  /**
   * @param Request $request
   * @return void
   */
  public function setRequest(Request $request): void
  {
    $this->request = $request;
  }

  /**
   * @return BootstrapAbstract
   */
  public function getBootstrap(): BootstrapAbstract
  {
    return $this->bootstrap;
  }

  /**
   * @param BootstrapAbstract $bootstrap
   * @return void
   */
  public function setBootstrap(BootstrapAbstract $bootstrap): void
  {
    $this->bootstrap = $bootstrap;
  }

  /**
   * @return Loader
   */
  public function getLoader(): Loader
  {
    return $this->loader;
  }

  /**
   * @param Loader $loader
   * @return void
   */
  public function setLoader(Loader $loader): void
  {
    $this->loader = $loader;
  }

  /**
   * @return View
   */
  public function getView(): View
  {
    return $this->_view;
  }

  /**
   * @param View $view
   * @return void
   */
  public function setView(View $view): void
  {
    $this->_view = $view;
  }

  /**
   * @return $this
   * @throws ReflectionException
   * @throws Exception
   */
  public function bootstrap(): Front
  {
    set_error_handler(function ($number, $message, $file, $line) {
      throw new Exception(implode(':', [$message, $file, $line]), $number);
    });

    if ($this->bootstrap) {

      $bootReflection = new ReflectionClass(
        $this->bootstrap
      );

      foreach ($bootReflection->getMethods() as $method) {

        if ($method->class != 'Air\\Core\\BootstrapAbstract') {
          $this->bootstrap->{$method->name}();
        }
      }
    }

    return $this;
  }

  /**
   * @param Exception|Error|null $exception
   * @return string|void
   *
   * @throws Exception
   * @throws Throwable
   */
  public function run(Exception|Error $exception = null)
  {
    if (!$this->request) {

      $this->request = new Request();

      if (php_sapi_name() !== 'cli') {
        $this->request->fillRequestFromServer();
      } else {
        $this->request->fillRequestFromCli();
      }
    }

    if (!$this->response) {
      $this->response = new Response();
    }

    try {

      if (!$this->router) {
        $this->router = new Router();
        $this->router->setRoutes($this->config['router'] ?? []);
        $this->router->setRequest($this->request);
        $this->router->parse();
      }

      $this->config['air'] = array_replace_recursive(
        $this->config['air'],
        $this->router->getConfig()
      );

      foreach ($this->config['air']['phpIni'] ?? [] as $key => $val) {
        ini_set($key, $val);
      }

      foreach ($this->config['air']['startup'] ?? [] as $key => $val) {

        if (function_exists($key)) {

          if (!is_array($val)) {
            $val = [$val];
          }

          call_user_func_array($key, $val);
        }
      }

      foreach ($this->config['air']['headers'] ?? [] as $key => $val) {
        $this->response->setHeader($key, $val);
      }

      $this->_view = new View();

      $this->_view->setIsMinifyHtml($this->config['air']['view']['minify'] ?? false);

      $modules = null;

      if ($this->config['air']['modules'] ?? false) {
        $modules = implode('/', array_slice(explode('\\', $this->config['air']['modules']), 2));

        $viewPath = realpath(implode('/', [
          $this->config['air']['loader']['path'],
          $modules,
          ucfirst($this->router->getModule()),
          'View'
        ]));

      } else {
        $viewPath = realpath(implode('/', [
          $this->config['air']['loader']['path'],
          'View'
        ]));
      }

      if ($viewPath) {
        $this->_view->setPath($viewPath);
        $this->_view->setLayoutEnabled(true);
        $this->_view->setLayoutTemplate('index');
        $this->_view->setScript($this->router->getController() . '/' . $this->router->getAction());

      } else {
        $this->_view->setAutoRender(false);
        $this->_view->setLayoutEnabled(false);
      }

      $controllerClassName = $this->getControllerClassName($this->router);

      if (!class_exists($controllerClassName, true) || !is_subclass_of($controllerClassName, '\\Air\\Core\\Controller')) {

        if ($exception) {
          throw $exception;
        }

        throw new \Air\Core\Exception\ControllerClassWasNotFound($controllerClassName);
      }

      /** @var Controller|ErrorController $controller */
      $controller = new $controllerClassName();

      $controller->setRequest($this->request);
      $controller->setResponse($this->response);
      $controller->setRouter($this->router);
      $controller->setView($this->_view);

      if ($exception && is_subclass_of($controller, '\\Air\\Core\\ErrorController')) {
        $controller->setException($exception);
        $controller->setExceptionEnabled($this->config['air']['exception'] ?? false);

      } else if ($exception) {
        throw $exception;
      }

      /** @var Plugin[] $plugins */

      $plugins = [];

      $pluginsPaths = [
        '\\' . $this->config['air']['loader']['namespace'] . '\\Plugin\\' => realpath($this->config['air']['loader']['path'] . '/Plugin')
      ];

      if ($modules) {

        $pluginsPaths['\\' . $this->config['air']['loader']['namespace'] . '\\' . $modules . '\\' . ucfirst($this->router->getModule()) . '\\Plugin\\'] =
          realpath(implode('/', [
            $this->config['air']['loader']['path'],
            $modules,
            ucfirst($this->router->getModule()),
            'Plugin'
          ]));
      }

      foreach (array_filter($pluginsPaths) as $pluginNamespace => $pluginsPath) {

        foreach (glob($pluginsPath . '/*.php') as $pluginClass) {

          $pluginClassName = $pluginNamespace . str_replace('.php', '', basename($pluginClass));

          if (is_subclass_of($pluginClassName, '\\Air\\Core\\Plugin')) {
            $plugins[] = new $pluginClassName();
          }
        }
      }

      foreach ($plugins as $plugin) {
        $plugin->preRun($this->request, $this->response, $this->router);
      }

      $controller->init();

      if (is_callable([$controller, $this->router->getAction()])) {

        if (in_array($this->router->getAction(), get_class_methods(Controller::class))) {
          throw new ActionMethodIsReserved($this->router->getAction());
        }

        $content = call_user_func_array(
          [$controller, $this->router->getAction()],
          $this->inject($controller, $this->router, $this->request)
        );

        $needLayout = true;

        if (!is_null($content)) {
          $needLayout = false;
        }

        $controller->postRun();

        if (is_null($content) && $this->_view->isAutoRender()) {
          $content = $this->_view->render();
        }

        if (is_array($content)) {
          $content = json_encode($content);
          $this->response->setHeader('Content-type', 'application/json');

        } else if ($this->_view->isLayoutEnabled() && $needLayout) {
          $this->_view->setContent($content ?? '');
          $content = $this->_view->renderLayout();
        }

        $this->response->setBody($content);

        foreach ($plugins as $plugin) {
          $plugin->postRun($this->request, $this->response, $this->router);
        }
      } else {
        throw new \Air\Core\Exception\ActionMethodWasNotFound($this->router->getAction());
      }

      return $this->render($this->response);
    } catch (Throwable $localException) {

      if ($localException instanceof Stop) {
        return $this->render($this->response);
      }

      if (!$exception) {

        $errorRouter = new Router();

        $errorRouter->setRequest($this->request);
        $errorRouter->setModule($this->router->getModule());
        $errorRouter->setController('error');
        $errorRouter->setAction('index');
        $errorRouter->setRoutes($this->config['router'] ?? []);
        $errorRouter->setConfig($this->router->getConfig());

        $this->setRouter($errorRouter);
        return $this->run($localException);
      }

      if ($this->config['air']['exception'] ?? false) {
        throw $localException;
      }
    }
  }

  /**
   * @param Router $router
   * @return string
   */
  public function getControllerClassName(Router $router): string
  {
    $module = $router->getModule();
    $controller = $router->getController();

    if (($this->getConfig()['air']['storage']['route'] ?? false) === $controller) {
      return Storage::class;

    } else if (($this->getConfig()['air']['admin']['auth']['route'] ?? false) === $controller) {
      return Login::class;

    } else if (($this->getConfig()['air']['admin']['system'] ?? false) === $controller) {
      return System::class;

    } else if (($this->getConfig()['air']['admin']['manage'] ?? false) === $controller) {
      return Admin::class;

    } else if (($this->getConfig()['air']['admin']['history'] ?? false) === $controller) {
      return History::class;

    } else if (($this->getConfig()['air']['admin']['fonts'] ?? false) === $controller) {
      return Font::class;

    } else if (($this->getConfig()['air']['logs']['route'] ?? false) === $controller) {
      return Log::class;

    } else if (($this->getConfig()['air']['admin']['codes'] ?? false) === $controller) {
      return Codes::class;

    } else if (($this->getConfig()['air']['admin']['languages'] ?? false) === $controller) {
      return Language::class;

    } else if (($this->getConfig()['air']['admin']['phrases'] ?? false) === $controller) {
      return Phrase::class;

    } else if (($this->getConfig()['air']['admin']['robotsTxt'] ?? false) === $controller) {
      return RobotsTxt::class;

    } else if ('robots.txt' === $controller) {
      return RobotsTxtUi::class;

    } else if ('fonts' === $controller) {
      return FontsUi::class;
    }

    if ($this->config['air']['modules'] ?? false) {
      return implode('\\', [
        $this->config['air']['modules'],
        ucfirst($module),
        'Controller',
        ucfirst($controller),
      ]);
    }

    return implode('\\', [
      $this->config['air']['loader']['namespace'],
      'Controller',
      ucfirst($controller),
    ]);
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

    if ($this->bootstrap) {
      $this->bootstrap->setConfig($config);
    }
  }

  /**
   * @param Controller $controller
   * @param Router $router
   * @param Request $request
   *
   * @return array
   * @throws ReflectionException
   */
  public function inject(Controller $controller, Router $router, Request $request): array
  {
    $reflection = new ReflectionMethod($controller, $router->getAction());
    $injector = $router->getInjector();

    $docComment = $reflection->getDocComment();

    if ($docComment) {
      $docComment = str_replace('*', '', $reflection->getDocComment());

      $docComment = array_filter(array_map(function ($line) {

        $line = trim($line);

        if (strlen($line) > 0) {
          return $line;
        }
        return null;
      }, explode("\n", $docComment)));

      $params = [];

      foreach ($docComment as $line) {

        if (str_starts_with($line, '@param')) {

          try {
            $param = explode('|', explode('$', $line)[1]);
          } catch (Exception) {
          }

          $var = trim($param[0]);

          $main = trim((explode('&', ($param[1] ?? 'id'))[0]));
          $main = strlen($main) ? $main : 'id';

          try {
            $cond = json_decode(explode('&', $param[1])[1] ?? [], true) ?? [];
          } catch (Throwable) {
            $cond = [];
          }

          $params[$var] = [
            'main' => $main,
            'cond' => $cond
          ];
        }
      }
    }

    $args = [];

    foreach ($reflection->getParameters() as $parameter) {

      $var = $parameter->getName();

      if (isset($injector[$var])) {
        $args[$var] = $injector[$var]($router->getUrlParams()[$var] ?? null);
      } else {

        $value = $router->getUrlParams()[$var] ?? $request->getParam($var);
        $defaultValue = $parameter->isDefaultValueAvailable() ? $parameter->getDefaultValue() : null;

        if (!$parameter->getType()) {
          $args[$var] = $value;
          continue;
        }

        switch ($parameter->getType()->getName()) {

          case 'int':
            $args[$var] = $value !== null ? intval($value) : $defaultValue;
            break;

          case 'string':
            $args[$var] = $value !== null ? strval($value) : $defaultValue;
            break;

          case 'bool':
            $args[$var] = $value !== null ? boolval($value) : $defaultValue;
            break;

          case 'array':
            $args[$var] = $value !== null ? (array)$value : $defaultValue;
            break;

          default:
            $className = $parameter->getType()->getName();

            try {

              /** @var ModelAbstract $model */
              $model = new $className();

              if (is_subclass_of($model, '\\Air\\Model\\ModelAbstract')) {

                if (isset($params[$parameter->getName()])) {

                  $property = $model->getMeta()->getPropertyWithName(
                    $params[$parameter->getName()]['main']
                  );

                  try {
                    settype($value, $property->getType());
                  } catch (Exception) {
                    $value = (string)$value;
                  }

                  if ($params[$parameter->getName()]['main'] === 'id') {
                    $allCond = ['$or' => [
                      ['id' => $value],
                      ['url' => $value],
                    ]];
                    $userCond = [];
                  } else {
                    $allCond = [$params[$parameter->getName()]['main'] => $value];
                    $userCond = ($params[$parameter->getName()]['cond'] ?? []);
                  }

                  if (count($userCond)) {
                    $allCond = [...$userCond, ...$allCond];
                  }

                  /** @var ModelAbstract $className */
                  $args[$var] = $className::fetchOne($allCond);
                }
              } else {
                $args[$var] = new $className($value);
              }
            } catch (Exception) {
              $args[$var] = $value;
            }
        }
      }
    }

    return $args;
  }

  /**
   * @param Response $response
   * @return mixed
   */
  public function render(Response $response): mixed
  {
    /** Setup Status **/
    $statusCode = $response->getStatusCode();

    $phpSapiName = substr(php_sapi_name(), 0, 3);

    if ($phpSapiName == 'cli') {
      return null;
    }

    if ($phpSapiName == 'cgi' || $phpSapiName == 'fpm') {
      header('Status: ' . $statusCode);
    } else {
      $protocol = $_SERVER['SERVER_PROTOCOL'] ?? 'HTTP/1.0';
      header($protocol . ' ' . $statusCode);
    }

    /** Setup Headers **/
    foreach ($response->getHeaders() as $name => $value) {
      header($name . ': ' . $value);
    }

    return $response->getBody();
  }

  /**
   * @throws Stop
   */
  public function stop()
  {
    throw new Stop();
  }
}
