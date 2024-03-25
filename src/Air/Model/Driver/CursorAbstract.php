<?php

declare(strict_types=1);

namespace Air\Model\Driver;

use ArrayAccess;
use Countable;
use Iterator;
use Air\Core\Exception\ClassWasNotFound;
use Air\Model\Driver\Exception\UnsupportedCursorOperation;
use Air\Model\Exception\CallUndefinedMethod;
use Air\Model\Exception\ConfigWasNotProvided;
use Air\Model\Exception\DriverClassDoesNotExists;
use Air\Model\Exception\DriverClassDoesNotExtendsFromDriverAbstract;
use Air\Model\ModelAbstract;

abstract class CursorAbstract implements Iterator, ArrayAccess, Countable
{
  /**
   * @var ModelAbstract[]
   */
  protected array $documents = [];

  /**
   * @var string|ModelAbstract
   */
  private string|ModelAbstract $model;

  /**
   * @var int
   */
  private int $cursorIndex = 0;

  /**
   * @var array
   */
  private array $cursorData;

  /**
   * @var array|null
   */
  private ?array $config;

  /**
   * CursorAbstract constructor.
   *
   * @param ModelAbstract $model
   * @param array $data
   * @param array $config
   */
  public function __construct(ModelAbstract $model, array $data, array $config = [])
  {
    $this->model = $model;
    $this->cursorData = $data;
    $this->config = $config;
  }

  /**
   * @return array|null
   */
  public function getConfig(): ?array
  {
    return $this->config;
  }

  /**
   * @return array
   * @throws CallUndefinedMethod
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   * @throws Exception\PropertyHasDifferentType
   * @throws Exception\PropertyWasNotFound
   * @throws ClassWasNotFound
   */
  public function toArray(): array
  {
    $arrayData = [];
    foreach ($this as $document) {
      $arrayData[] = $document->toArray();
    }
    return $arrayData;
  }

  /**
   * @return int
   */
  public function getCursorIndex(): int
  {
    return $this->cursorIndex;
  }

  /**
   * @param int $cursorIndex
   * @return void
   */
  public function setCursorIndex(int $cursorIndex): void
  {
    $this->cursorIndex = $cursorIndex;
  }

  /**
   * @return int
   * @throws CallUndefinedMethod
   * @throws ClassWasNotFound
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   */
  public function save(): int
  {
    $savedCount = 0;
    foreach ($this->documents as $document) {
      $savedCount += $document->save();
    }
    return $savedCount;
  }

  /**
   * @param int $offset
   * @return bool
   */
  public function offsetExists($offset): bool
  {
    if (is_numeric($offset)) {
      $data = $this->getCursorData();
      return isset($data[$offset]);
    }
    return false;
  }

  /**
   * @return array
   */
  public function getCursorData(): array
  {
    return $this->cursorData;
  }

  /**
   * @param array $cursorData
   * @return void
   */
  public function setCursorData(array $cursorData): void
  {
    $this->cursorData = $cursorData;
  }

  /**
   * @param mixed $offset
   * @return mixed
   * @throws CallUndefinedMethod
   * @throws ClassWasNotFound
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   */
  public function offsetGet(mixed $offset): mixed
  {
    return $this->getRowWithIndex($this->getCursorData(), $offset);
  }

  /**
   * @param array $data
   * @param int $index
   * @return mixed
   * @throws CallUndefinedMethod
   * @throws ClassWasNotFound
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   */
  public function getRowWithIndex(array $data, int $index): mixed
  {
    if (isset($this->documents[$index])) {
      return $this->documents[$index];
    }

    if (isset($data[$index])) {

      $modelClassName = $this->getModel()->getModelClassName();

      /** @var ModelAbstract $model */
      $model = new $modelClassName();
      $model->populate(static::processDataRow($data[$index]), false);

      $this->documents[$index] = $model;

      return $model;
    }

    return null;
  }

  /**
   * @return ModelAbstract
   */
  public function getModel(): ModelAbstract
  {
    return $this->model;
  }


  /*************** \ArrayIterator implementation ***********/

  /**
   * @param ModelAbstract $model
   * @return void
   */
  public function setModel(ModelAbstract $model): void
  {
    $this->model = $model;
  }

  /**
   * @param array $data
   * @return array
   */
  public function processDataRow(array $data): array
  {
    return $data;
  }

  /**
   * @param mixed|null $offset
   * @param mixed|null $value
   * @return void
   * @throws UnsupportedCursorOperation
   */
  public function offsetSet(mixed $offset = null, mixed $value = null): void
  {
    throw new UnsupportedCursorOperation("offsetSet - " . $offset);
  }

  /**
   * @param mixed|null $offset
   * @return void
   * @throws UnsupportedCursorOperation
   */
  public function offsetUnset(mixed $offset = null): void
  {
    throw new UnsupportedCursorOperation("offsetUnset - " . $offset);
  }


  /*************** \Iterator implementation ***********/

  /**
   * @return ModelAbstract|$this
   * @throws CallUndefinedMethod
   * @throws ClassWasNotFound
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   */
  public function current(): static|ModelAbstract
  {
    return $this->getRowWithIndex($this->getCursorData(), $this->cursorIndex);
  }

  /**
   * return null
   */
  public function next(): void
  {
    $this->cursorIndex++;
  }

  /**
   * @return int|null
   */
  public function key(): mixed
  {
    if (isset($this->cursorData[$this->cursorIndex])) {
      return $this->cursorIndex;
    }
    return null;
  }

  /**
   * @return bool
   */
  public function valid(): bool
  {
    return isset($this->cursorData[$this->cursorIndex]);
  }

  /**
   * @return void
   */
  public function rewind(): void
  {
    $this->cursorIndex = 0;
  }


  /*************** \Countable implementation ***********/

  /**
   * @return int
   */
  public function count(): int
  {
    return count($this->cursorData);
  }
}
