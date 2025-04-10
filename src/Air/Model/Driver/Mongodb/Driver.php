<?php

declare(strict_types=1);

namespace Air\Model\Driver\Mongodb;

use Air\Model\Driver\CursorAbstract;
use Air\Model\Driver\DriverAbstract;
use Air\Model\ModelAbstract;
use Air\Type\TypeAbstract;
use MongoDB\BSON\ObjectId;
use MongoDB\Driver\BulkWrite;
use MongoDB\Driver\Command;
use MongoDB\Driver\Manager;
use MongoDB\Driver\Query;
use MongoDB\Driver\WriteConcern;
use Throwable;

class Driver extends DriverAbstract
{
  private ?array $manager = null;

  public function save(): int
  {
    $bulk = new BulkWrite();

    $data = $this->replaceIdToObjectId(
      $this->getModel()->getData()
    );

    $data = $this->normalizeDataTypes($data);

    if ($this->getModel()->getMeta()->hasProperty('updatedAt')) {
      $updatedAtProperty = $this->getModel()->getMeta()->getPropertyWithName('updatedAt');
      if ($updatedAtProperty->getType() === 'integer') {
        $data['updatedAt'] = time();
      }
    }

    if ($this->getModel()->id) {
      $cond = $this->replaceIdToObjectId(['id' => $this->getModel()->id]);
      $bulk->update($cond, ['$set' => $data], ['multi' => true, 'upsert' => false]);
    } else {
      if ($this->getModel()->getMeta()->hasProperty('createdAt')) {
        $createdAtProperty = $this->getModel()->getMeta()->getPropertyWithName('createdAt');
        if ($createdAtProperty->getType() === 'integer') {
          $data['createdAt'] = time();
        }
      }
      $this->getModel()->id = (string)$bulk->insert($data);
    }

    $writeConcern = new WriteConcern(
      WriteConcern::MAJORITY, 1000
    );

    $result = $this->getManager()->executeBulkWrite(
      $this->getCollectionNamespace(), $bulk, $writeConcern
    );

    if (!$this->getModel()->id) {
      return $result->getInsertedCount();
    }

    return $result->getModifiedCount();
  }

  private function replaceIdToObjectId(array $cond = []): array
  {
    if (!is_array($cond)) {
      $cond = [$cond];
    }
    if (is_array($cond) && array_key_exists('id', $cond)) {
      $cond['_id'] = new ObjectId(
        !empty($cond['id']) ? $cond['id'] : null
      );
      unset($cond['id']);
    }
    return $cond;
  }

  private function normalizeDataTypes(array $data = []): array
  {
    foreach ($data as $name => $value) {

      if ($value instanceof ModelAbstract) {
        $data[$name] = $value->{$value->getMeta()->getPrimary()};

      } else if ($value instanceof Cursor) {
        $ids = [];
        foreach ($value as $record) {
          $ids[] = $record->{$value->getModel()->getMeta()->getPrimary()};
        }
        $data[$name] = $ids;

      } else if ($value instanceof TypeAbstract) {
        $data[$name] = $value->toRaw();

      } else {
        try {
          $typeName = $this->getModel()->getMeta()->getPropertyWithName($name)->getType();

          if (str_ends_with($typeName, '[]') && is_subclass_of(substr($typeName, 0, -2), TypeAbstract::class)) {
            $data[$name] = [];

            /** @var TypeAbstract[] $value */
            foreach ($value as $item) {
              if (is_object($item)) {
                $data[$name][] = $item->toRaw();
              } else {
                $data[$name][] = $item;
              }
            }
          }
        } catch (Throwable) {
        }
      }
    }

    return $data;
  }

  public function getManager(): Manager
  {
    $managerConfigKey = md5(var_export([], true));

    if (empty($this->manager[$managerConfigKey])) {

      $config = $this->getConfig();

      $credentials = null;
      if (isset($config['username']) && isset($config['password'])) {
        $credentials = implode(':', [$config['username'], $config['password']]) . '@';
      }

      $servers = [];

      foreach ($config['servers'] as $server) {
        $servers[] = implode(':', [$server['host'], $server['port']]);
      }

      $servers = implode(',', $servers) . '/' . $config['db'];

      $replicaSetName = null;
      if (!empty($config['replicaSetName'])) {
        $replicaSetName = '?replicaSet=' . $config['replicaSetName'];
      }

      $connection = 'mongodb://' . $credentials . $servers . $replicaSetName;

      $this->manager[$managerConfigKey] = new Manager($connection);
    }
    return $this->manager[$managerConfigKey];
  }

  public function getCollectionNamespace(): string
  {
    return implode('.', [
      $this->getConfig()['db'],
      $this->getModel()->getMeta()->getCollection()
    ]);
  }

  public function remove(array $cond = [], int $limit = null): int
  {
    if ($this->getModel()->id) {

      $cond = $this->replaceIdToObjectId([
        'id' => $this->getModel()->id
      ]);

      $limit = 1;
    } else {
      list($cond) = $this->processQuery($cond);
      $cond = $this->normalizeDataTypes($cond);
    }

    $bulk = new BulkWrite();

    $bulk->delete($cond, ['limit' => $limit]);

    $writeConcern = new WriteConcern(
      WriteConcern::MAJORITY, 100
    );

    $result = $this->getManager()->executeBulkWrite(
      $this->getCollectionNamespace(), $bulk, $writeConcern
    );

    return $result->getDeletedCount();
  }

  private function processQuery(array $cond = [], array $sort = []): array
  {
    if ($cond === null) {
      $cond = [];
    }
    $cond = $this->replaceIdToObjectId($cond);
    if ($sort === null) {
      $sort = [];
    }
    return [$cond, $sort];
  }

  private function getProjection(array $map = []): array
  {
    $projection = [];
    foreach ($map as $field) {
      $projection[$field] = 1;
    }
    return $projection;
  }

  public function fetchOne(array $cond = [], array $sort = [], array $map = []): mixed
  {
    list($cond, $sort) = $this->processQuery($cond, $sort);

    $cond = $this->normalizeDataTypes($cond);
    $projection = $this->getProjection($map);

    $query = new Query($cond, [
      'limit' => 1,
      'sort' => $sort,
      'projection' => $projection
    ]);

    $cursor = new Cursor($this->getModel(), $query, $this->getConfig());

    if ($cursor->offsetExists(0)) {
      return $cursor->offsetGet(0);
    }
    return null;
  }

  public function fetchAll(
    array $cond = [],
    array $sort = [],
    int   $count = null,
    int   $offset = null,
    array $map = []
  ): array|CursorAbstract
  {
    list($cond, $sort) = $this->processQuery($cond, $sort);

    $cond = $this->normalizeDataTypes($cond);
    $projection = $this->getProjection($map);

    $query = new Query($cond, [
      'sort' => $sort,
      'skip' => $offset,
      'limit' => $count,
      'projection' => $projection
    ]);

    return new Cursor($this->getModel(), $query, $this->getConfig());
  }

  public function count(array $cond = []): int
  {
    list($cond) = $this->processQuery($cond);

    $cond = $this->normalizeDataTypes($cond);

    if (!count($cond)) {
      $cond = null;
    }

    $command = new Command([
      'count' => $this->getModel()->getMeta()->getCollection(),
      'query' => $cond
    ]);

    try {
      $cursor = $this->getManager()->executeCommand($this->getConfig()['db'], $command);
      $res = current($cursor->toArray());
      return $res->n;

    } catch (Throwable) {
    }

    return 0;
  }

  public function batchInsert(array $data = null): int
  {
    $bulk = new BulkWrite();

    foreach ($data as $dataItem) {

      $modelClassName = $this->getModel()->getModelClassName();

      /** @var ModelAbstract $model */
      $model = new $modelClassName();

      if ($model->getMeta()->hasProperty('updatedAt')) {
        $updatedAtProperty = $model->getMeta()->getPropertyWithName('updatedAt');
        if ($updatedAtProperty->getType() === 'integer') {
          $dataItem['updatedAt'] = time();
        }
      }

      if ($model->getMeta()->hasProperty('createdAt')) {
        $updatedAtProperty = $model->getMeta()->getPropertyWithName('createdAt');
        if ($updatedAtProperty->getType() === 'integer') {
          $dataItem['createdAt'] = time();
        }
      }

      $model->populate($dataItem);

      $bulk->insert(
        $this->normalizeDataTypes(
          $model->getData()
        )
      );
    }

    if ($bulk->count()) {

      $collectionNamespace = $this->getCollectionNamespace();

      $writeResults = $this->getManager()->executeBulkWrite($collectionNamespace, $bulk);
      return $writeResults->getInsertedCount();
    }

    return 0;
  }

  public function insert(array $data = []): int
  {
    return self::batchInsert([$data]);
  }

  public function update(array $cond = [], array $data = []): int
  {
    list($cond) = $this->processQuery($cond);

    $cond = $this->normalizeDataTypes($cond);

    $modelClassName = $this->getModel()->getModelClassName();

    /** @var ModelAbstract $model */
    $model = new $modelClassName();
    $model->populate($data);

    $bulk = new BulkWrite();

    $bulk->update(
      $cond,
      ['$set' => $this->normalizeDataTypes($model->getData())],
      ['multi' => true]
    );

    if ($bulk->count()) {
      $collectionNamespace = $this->getCollectionNamespace();

      $writeResults = $this->getManager()->executeBulkWrite($collectionNamespace, $bulk);
      return $writeResults->getInsertedCount();
    }

    return 0;
  }
}
