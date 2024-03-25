<?php

declare(strict_types=1);

namespace Air\Core;

abstract class BootstrapAbstract
{
  /**
   * @var array
   */
  private array $config = [];

  /**
   * @return array
   */
  final public function getConfig(): array
  {
    return $this->config;
  }

  /**
   * @param array $config
   */
  final public function setConfig(array $config): void
  {
    $this->config = $config;
  }
}