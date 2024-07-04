<?php

namespace Air\View;

use Air\Type\Meta;
use Exception;
use Air\Core\Exception\ClassWasNotFound;
use Air\Core\Exception\ViewTemplateWasNotFound;
use Air\Core\Front;
use Air\View\ViewHelper\HelperAbstract;
use ReflectionClass;

class View
{
  /**
   * @var string|null
   */
  protected ?string $path = null;

  /**
   * @var bool
   */
  protected bool $layoutEnabled = true;

  /**
   * @var string|null
   */
  protected ?string $layoutTemplate = null;

  /**
   * @var string|null
   */
  protected ?string $script = null;

  /**
   * @var bool
   */
  protected bool $autoRender = true;

  /**
   * @var string|null
   */
  protected ?string $content = null;

  /**
   * @var array
   */
  protected array $vars = [];

  /**
   * @var bool
   */
  protected bool $isMinifyHtml = false;

  /**
   * @var Meta|null
   */
  protected ?Meta $meta = null;

  /**
   * @var \Closure|null
   */
  protected ?\Closure $defaultMeta = null;

  /**
   * @var array
   */
  protected array $properties = [];

  public function __construct()
  {
    foreach ((new ReflectionClass($this))->getProperties() as $property) {
      $this->properties[] = $property->getName();
    }
  }

  /**
   * @param string $key
   * @param mixed $value
   * @return void
   * @throws Exception
   */
  public function assign(string $key, mixed $value): void
  {
    if (in_array(strtolower($key), $this->properties)) {
      throw new Exception("Var name '$key' is predefined");
    }

    $this->vars[$key] = $value;
  }

  /**
   * @return array
   */
  public function getVars(): array
  {
    return $this->vars;
  }

  /**
   * @param array $vars
   */
  public function setVars(array $vars): void
  {
    foreach ($vars as $key => $value) {
      $this->assign($key, $value);
    }
  }

  /**
   * @return string
   */
  public function getPath(): string
  {
    return $this->path;
  }

  /**
   * @param string $path
   */
  public function setPath(string $path): void
  {
    $this->path = $path;
  }

  /**
   * @return bool
   */
  public function isLayoutEnabled(): bool
  {
    return $this->layoutEnabled;
  }

  /**
   * @param bool $layoutEnabled
   */
  public function setLayoutEnabled(bool $layoutEnabled): void
  {
    $this->layoutEnabled = $layoutEnabled;
  }

  /**
   * @return bool
   */
  public function isMinifyHtml(): bool
  {
    return $this->isMinifyHtml;
  }

  /**
   * @param bool $isMinifyHtml
   */
  public function setIsMinifyHtml(bool $isMinifyHtml): void
  {
    $this->isMinifyHtml = $isMinifyHtml;
  }

  /**
   * @param Meta|null $meta
   */
  public function setMeta(?Meta $meta): void
  {
    $this->meta = $meta;
  }

  /**
   * @return Meta|null
   */
  public function getMeta(): ?Meta
  {
    return $this->meta;
  }

  /**
   * @param \Closure|null $defaultMeta
   */
  public function setDefaultMeta(\Closure $defaultMeta): void
  {
    $this->defaultMeta = $defaultMeta;
  }

  /**
   * @return \Closure|null
   */
  public function getDefaultMeta(): ?\Closure
  {
    return $this->defaultMeta;
  }

  /**
   * @param string $name
   * @return mixed|null
   */
  public function __get(string $name)
  {
    return $this->vars[$name] ?? null;
  }

  /**
   * @param string $name
   * @param $value
   */
  public function __set(string $name, $value)
  {
    $this->vars[$name] = $value;
  }

  /**
   * @param string $name
   * @param array $arguments
   * @return mixed
   * @throws ClassWasNotFound
   */
  public function __call(string $name, array $arguments)
  {
    if (Front::getInstance()->getConfig()['air']['modules'] ?? false) {

      $helperClassName = implode('\\', [
        Front::getInstance()->getConfig()['air']['modules'],
        ucfirst(Front::getInstance()->getRouter()->getModule()),
        'View',
        'Helper',
        ucfirst($name)
      ]);
    } else {
      $helperClassName = implode('\\', [
        Front::getInstance()->getConfig()['air']['loader']['namespace'],
        'View',
        'Helper',
        ucfirst($name)
      ]);
    }

    if (!class_exists($helperClassName)) {
      $helperClassName = implode('\\', ['Air', 'View', 'Helper', ucfirst($name)]);
    }

    /** @var HelperAbstract $helper */
    $helper = new $helperClassName();
    $helper->setView($this);

    return call_user_func_array([$helper, 'call'], $arguments);
  }

  /**
   * @return string
   */
  public function getLayoutTemplate(): string
  {
    return $this->layoutTemplate;
  }

  /**
   * @param string $layoutTemplate
   */
  public function setLayoutTemplate(string $layoutTemplate): void
  {
    $this->layoutTemplate = $layoutTemplate;
  }

  /**
   * @return string
   */
  public function getScript(): string
  {
    return $this->script;
  }

  /**
   * @param string $script
   */
  public function setScript(string $script): void
  {
    $this->script = $script;
  }

  /**
   * @return bool
   */
  public function isAutoRender(): bool
  {
    return $this->autoRender;
  }

  /**
   * @param bool $autoRender
   */
  public function setAutoRender(bool $autoRender): void
  {
    $this->autoRender = $autoRender;
  }

  /**
   * @return string
   */
  public function getContent(): string
  {
    return $this->content;
  }

  /**
   * @param string $content
   */
  public function setContent(string $content): void
  {
    $this->content = $content;
  }

  /**
   * @param string|null $template
   * @param array $vars
   * @return string
   * @throws Exception
   * @throws ClassWasNotFound
   */
  public function render(string $template = null, array $vars = []): string
  {
    if (count($vars)) {

      $view = new self();

      $view->setPath($this->path);
      $view->setVars($vars);

      return $view->render($template);
    }

    try {

      $_template = $this->path . '/Scripts/' . ($template ?? $this->script) . '.phtml';

      if (!file_exists($_template)) {
        throw new ViewTemplateWasNotFound($_template);
      }

      $content = $this->_render($this->path . '/Scripts/' . ($template ?? $this->script) . '.phtml');

    } catch (ViewTemplateWasNotFound) {

      $config = Front::getInstance()->getConfig();

      if ($config['air']['modules'] ?? false) {
        $modules = implode('/', array_slice(explode('\\', $config['air']['modules']), 2));

        $viewPath = realpath(implode('/', [
          $config['air']['loader']['path'],
          $modules,
          ucfirst(Front::getInstance()->getRouter()->getModule()),
          'View'
        ]));

      } else {
        $viewPath = realpath(implode('/', [
          $config['air']['loader']['path'],
          'View'
        ]));
      }

      $content = $this->_render($viewPath . '/Scripts/' . ($template ?? $this->script) . '.phtml');
    }

    return $content;
  }

  /**
   * @param string $template
   * @return string
   * @throws Exception
   */
  private function _render(string $template): string
  {
    $exception = null;

    ob_start();

    try {
      include $template;
    } catch (Exception $e) {
      $exception = $e;
    }

    $content = ob_get_contents();

    ob_end_clean();

    if ($exception) {
      throw $exception;
    }

    if ($this->isMinifyHtml()) {
      $search = [
        // strip whitespaces after tags, except space
        '/\>[^\S ]+/s',

        // strip whitespaces before tags, except space
        '/[^\S ]+\</s',

        // shorten multiple whitespace sequences
        '/(\s)+/s',

        // Remove HTML comments
        '/<!--(.|\s)*?-->/'
      ];
      $replace = ['>', '<', '\\1', ''];
      $content = preg_replace($search, $replace, $content);
    }

    return $content;
  }

  /**
   * @return string
   * @throws Exception
   */
  public function renderLayout(): string
  {
    return $this->_render($this->path . '/Layouts/' . $this->layoutTemplate . '.phtml');
  }
}
