<?php

declare(strict_types=1);

namespace Air\Core;

use Air\Core\Exception\ClassWasNotFound;

class Loader
{
  private ?array $_config;

  public function __construct(array $config = [])
  {
    $this->_config = $config;

    spl_autoload_register(function ($className) use ($config) {

      $classFilePath = $this->getClassFilePath($className);

      if (!$classFilePath) {
        throw new ClassWasNotFound($classFilePath);
      }
      if (file_exists($classFilePath)) {
        require_once $classFilePath;
        return true;
      }
      return false;
    });
  }

  public function getClassFilePath(string $namespace): string
  {
    $namespace = explode('\\', $namespace);

    if ($namespace[0] == 'Air') {
      unset($namespace[0]);
      return implode('/', array_merge([realpath(__DIR__)], $namespace)) . '.php';

    } else if ($namespace[0] == $this->_config['air']['loader']['namespace']) {
      unset($namespace[0]);
      return implode('/', array_merge(
          [$this->_config['air']['loader']['path']],
          $namespace
        )) . '.php';
    }

    return implode('\\', $namespace);
  }
}