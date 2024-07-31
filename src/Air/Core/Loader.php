<?php

declare(strict_types=1);

namespace Air\Core;

use Air\Core\Exception\ClassWasNotFound;

/**
 * Class Loader
 * @package Air
 */
class Loader
{
  /**
   * @var array|null
   */
  private ?array $_config;

  /**
   * @param array $config
   * @throws ClassWasNotFound
   */
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

  /**
   * @param string $namespace
   * @return string
   */
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