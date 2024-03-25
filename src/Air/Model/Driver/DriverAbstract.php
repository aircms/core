<?php

declare(strict_types=1);

namespace Air\Model\Driver;

use Air\Model\ModelAbstract;

abstract class DriverAbstract
{
  /**
   * @var ModelAbstract|null
   */
  private ?ModelAbstract $model = null;

  /**
   * @var array
   */
  private array $config;

  /**
   * Driver constructor.
   * @param array $config
   */
  public function __construct(array $config = [])
  {
    $this->config = $config;
  }

  /**
   * @return array
   */
  public function getConfig(): array
  {
    return $this->config;
  }

  /**
   * @param array $cond
   * @param array $sort
   * @param array $map
   * @return ModelAbstract|$this
   */
  public function fetchObject(array $cond = [], array $sort = [], array $map = []): ModelAbstract|static
  {
    $model = static::fetchOne($cond, $sort, $map);
    if ($model) {
      return $model;
    }
    return $this->getModel();
  }

  /**
   * @param array $cond
   * @param array $sort
   * @param array $map
   * @return mixed
   */
  abstract public function fetchOne(array $cond = [], array $sort = [], array $map = []): mixed;

  /**
   * @return ModelAbstract
   */
  public function getModel(): ModelAbstract
  {
    return $this->model;
  }

  /**
   * @param ModelAbstract $model
   */
  public function setModel(ModelAbstract $model): void
  {
    $this->model = $model;
  }

  /**
   * @return int
   */
  abstract public function save(): int;

  /**
   * @param array $cond
   * @param int|null $limit
   * @return int
   */
  abstract public function remove(array $cond = [], int $limit = null): int;

  /**
   * @param array $cond
   * @param array $sort
   * @param int|null $count
   * @param int|null $offset
   * @param array $map
   * @return array|CursorAbstract
   */
  abstract public function fetchAll(
    array $cond = [],
    array $sort = [],
    int $count = null,
    int $offset = null,
    array $map = []
  ): array|CursorAbstract;

  /**
   * @param array $cond
   * @return int
   */
  abstract public function count(array $cond = []): int;

  /**
   * @param array $data
   * @return int
   */
  abstract public function batchInsert(array $data = []): int;

  /**
   * @param array $cond
   * @param array $data
   * @return int
   */
  abstract public function update(array $cond = [], array $data = []): int;
}