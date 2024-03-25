<?php

declare(strict_types=1);

namespace Air\Model\Driver\Mongodb;

use IteratorIterator;
use Air\Core\Exception\ClassWasNotFound;
use Air\Model\Driver\CursorAbstract;
use Air\Model\Driver\Exception\IndexOutOfRange;
use Air\Model\Exception\CallUndefinedMethod;
use Air\Model\Exception\ConfigWasNotProvided;
use Air\Model\Exception\DriverClassDoesNotExists;
use Air\Model\Exception\DriverClassDoesNotExtendsFromDriverAbstract;
use Air\Model\ModelAbstract;
use MongoDB\BSON\ObjectID;
use MongoDB\Driver\Exception\Exception;
use MongoDB\Driver\Manager;
use MongoDB\Driver\Query;
use Throwable;

class Cursor extends CursorAbstract
{
  /**
   * @var IteratorIterator|null
   */
  private ?IteratorIterator $iterator = null;

  /**
   * @var int
   */
  private int $count = -1;

  /**
   * @var Query|null
   */
  private ?Query $query;

  /**
   * Cursor constructor.
   *
   * @param ModelAbstract $model
   * @param Query $query
   * @param array $config
   */
  public function __construct(ModelAbstract $model, Query $query, array $config = [])
  {
    parent::__construct($model, [], $config);
    $this->query = $query;
  }

  /**
   * @param $offset
   * @return bool
   * @throws Exception
   */
  public function offsetExists($offset): bool
  {
    if (is_numeric($offset)) {

      $this->rewind();

      for ($i = 0; $i < $offset; $i++) {
        $this->next();
      }

      return $this->valid();
    }

    return false;
  }

  /*************** \ArrayIterator implementation ***********/

  /**
   * @return void
   * @throws Exception
   */
  public function rewind(): void
  {
    $cursor = $this->executeQuery($this->query);
    $this->iterator = new IteratorIterator($cursor);

    $this->iterator->rewind();
  }

  /**
   * @param Query $query
   * @return \MongoDB\Driver\Cursor
   * @throws Exception
   */
  private function executeQuery(Query $query): \MongoDB\Driver\Cursor
  {
    /** @var Manager $manager */
    $manager = $this->getModel()->getManager();

    return $manager->executeQuery(
      $this->getCollectionNamespace(),
      $query
    );
  }



  /*************** \Iterator implementation ***********/

  /**
   * @return string
   */
  public function getCollectionNamespace(): string
  {
    return implode('.', [
      $this->getConfig()['db'],
      $this->getModel()->getMeta()->getCollection()
    ]);
  }

  /**
   * @return void
   */
  public function next(): void
  {
    $this->iterator->next();
  }

  /**
   * @return bool
   */
  public function valid(): bool
  {
    return $this->iterator->valid();
  }

  /**
   * @param mixed $offset
   * @return mixed
   * @throws CallUndefinedMethod
   * @throws ClassWasNotFound
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   * @throws Exception
   * @throws IndexOutOfRange
   */
  public function offsetGet(mixed $offset): mixed
  {
    if (isset($this->documents[$offset])) {
      return $this->documents[$offset];
    }

    $this->rewind();

    for ($i = 0; $i < $offset; $i++) {

      try {
        $this->iterator->next();
      } catch (Throwable) {
        throw new IndexOutOfRange($offset, $i + 1);
      }
    }

    $this->documents[$offset] = $this->getDataModel();
    return $this->documents[$offset];
  }

  /**
   * @return ModelAbstract
   * @throws CallUndefinedMethod
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   * @throws ClassWasNotFound
   */
  private function getDataModel(): ModelAbstract
  {
    $currentItem = $this->iterator->current();

    if ($currentItem->_id instanceof ObjectID) {
      $currentItem->_id = (string)$currentItem->_id;
    }

    $data = json_decode(json_encode($currentItem), true);
    $modelClassName = $this->getModel()->getModelClassName();

    /** @var ModelAbstract $model */
    $model = new $modelClassName();
    $model->populateWithoutQuerying($this->processDataRow($data));

    return $model;
  }


  /*************** \Countable implementation ***********/

  /**
   * @param array $data
   * @return array
   */
  public function processDataRow(array $data): array
  {
    if (isset($data['_id'])) {
      $data['id'] = $data['_id'];
      unset($data['_id']);
    }

    return $data;
  }

  /**
   * @return ModelAbstract|$this
   * @throws CallUndefinedMethod
   * @throws ClassWasNotFound
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   */
  public function current(): ModelAbstract|static
  {
    $offset = $this->iterator->key();

    if (isset($this->documents[$offset])) {
      return $this->documents[$offset];
    }

    $this->documents[$offset] = $this->getDataModel();
    return $this->documents[$offset];
  }

  /**
   * @return int|null
   */
  public function key(): mixed
  {
    return $this->iterator->key();
  }

  /**
   * @return int
   * @throws Exception
   */
  public function count(): int
  {
    if ($this->count == -1) {

      $queryResult = $this->executeQuery($this->query);
      $this->count = count($queryResult->toArray());
    }

    return $this->count;
  }
}
