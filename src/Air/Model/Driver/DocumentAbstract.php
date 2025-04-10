<?php

declare(strict_types=1);

namespace Air\Model\Driver;

use Air\Cache;
use Air\Model\Driver\Exception\PropertyHasDifferentType;
use Air\Model\Meta\Property;
use Air\Model\ModelAbstract;
use ArrayAccess;
use MongoDB\BSON\ObjectId;

abstract class DocumentAbstract implements ArrayAccess
{
  private ?ModelAbstract $model;
  private array $data = [];

  public function __construct(ModelAbstract $model)
  {
    $this->model = $model;
  }

  public function populate(array $data, bool $fromSet = true): void
  {
    foreach ($this->getModel()->getMeta()->getProperties() as $property) {
      if (isset($data[$property->getName()])) {
        $this->setProperty($property, $data[$property->getName()], $fromSet);
      }
    }
  }

  public function populateWithoutQuerying(array $data): void
  {
    foreach ($this->getModel()->getMeta()->getProperties() as $property) {
      if (array_key_exists($property->getName(), $data)) {
        $this->setProperty($property, $data[$property->getName()], false, true);
      }
    }
  }

  public function getModel(): ?ModelAbstract
  {
    return $this->model;
  }

  public function setModel(ModelAbstract $model): void
  {
    $this->model = $model;
  }

  public function setProperty(
    Property $property,
    mixed    $value,
    bool     $fromSet = false,
    bool     $isPopulateWithoutQuerying = false
  ): void
  {
    if ($isPopulateWithoutQuerying) {
      $this->data[$property->getName()] = $value;
      return;
    }
    $this->data[$property->getName()] = $this->_castDataType($property, $value, $fromSet);
  }

  protected function _castDataType(Property $property, $value, bool $isSet = true, bool $toArray = false): mixed
  {
    if (in_array($property->getType(), ['integer', 'float', 'double', 'array', 'string', 'boolean', 'NULL'])) {
      settype($value, $property->getType());
      return $value;
    }

    if (
      str_ends_with($property->getType(), '[]')
      && class_exists(substr($property->getType(), 0, -2))
      && is_subclass_of(substr($property->getType(), 0, -2), ModelAbstract::class)
    ) {
      if (is_array($value)) {

        /** @var ModelAbstract $modelClassName */
        $modelClassName = substr($property->getType(), 0, -2);
        $modelClassObject = new $modelClassName;

        switch ($modelClassObject->getMeta()->getPrimary()) {

          case 'id':
            $objects = [];
            foreach ($value as $id) {
              $obj = $modelClassName::fetchOne(['_id' => new ObjectId(
                is_object($id) ?
                  /** @var ModelAbstract $id */
                  $id->{$id->getMeta()->getPrimary()} :
                  $id
              )]);

              if ($obj) {
                $objects[] = $obj;
              }
            }
            break;

          default:
            $objects = $modelClassName::fetchAll([
              $modelClassObject->getMeta()->getPrimary() => ['$in' => $value]
            ]);
        }

        if ($toArray) {

          if (is_array($objects)) {
            $_objects = [];
            foreach ($objects as $object) {
              if (is_object($object) && is_callable([$object, 'toArray'])) {
                $_objects[] = $object->toArray();
              } else {
                $_objects[] = $object;
              }
            }
            return $_objects;
          }

          return $objects->toArray();
        }

        return $objects;
      }

      if (!$isSet) {

        if ($toArray) {
          return $value ? $value->toArray() : [];
        }

        return (array)$value;
      }

      $records = [];
      foreach ($value as $record) {
        $records[] = $record->{$record->getMeta()->getPrimary()};
      }

      return $records;
    }

    if (
      class_exists($property->getType())
      && is_subclass_of($property->getType(), ModelAbstract::class)
    ) {
      if (is_string($value)) {

        /** @var ModelAbstract $modelClassName */
        $modelClassName = $property->getType();
        $modelClassObject = new $modelClassName;

        $object = $modelClassName::fetchOne([
          $modelClassObject->getMeta()->getPrimary() => $value
        ]);

        if ($toArray) {
          return $object->toArray();
        }

        return $object;
      } else if (is_null($value)) {
        return null;
      }

      if (!$isSet) {

        if ($toArray) {
          return $value->toArray();
        }

        return $value;
      }

      return $value->{$value->getMeta()->getPrimary()};
    }

    if (class_exists($property->getType())) {

      if (is_array($value)) {

        $typeClassName = $property->getType();
        $instance = new $typeClassName($value, $this->getModel());

        if ($toArray) {
          return (array)$instance;
        }

        return $instance;
      } else if (is_null($value)) {
        return null;
      }

      if (!$isSet) {
        if ($toArray) {
          return (array)$value;
        }
      }

      return $value;
    }


    if (
      str_ends_with($property->getType(), '[]')
      && class_exists(substr($property->getType(), 0, -2))
    ) {

      if (is_array($value)) {

        if ($isSet) {
          return $value;
        }

        $typeClassName = substr($property->getType(), 0, -2);

        $objects = [];
        $objectsA = [];

        foreach ($value as $item) {
          $objectsA[] = $item;

          if (is_object($item) && $typeClassName === $item::class) {
            $objects[] = $item;
          } else {
            $objects[] = new $typeClassName($item, $this->getModel());
          }
        }

        if ($toArray) {
          return $objectsA;
        }

        return $objects;

      } else if (is_null($value)) {
        return [];
      }

      if (!$isSet) {
        if ($toArray) {
          return (array)$value;
        }
      }

      return $value;
    }

    throw new PropertyHasDifferentType(
      $this->getModel()->getMeta()->getCollection(),
      $property->getName(),
      $property->getType(),
      gettype($value)
    );
  }

  public function __isset($name)
  {
    return isset($this->data[$name]);
  }

  public function toArray(): array
  {
    $arrayData = [];
    foreach ($this->getModel()->getMeta()->getProperties() as $property) {
      $arrayData[$property->getName()] = $this->getProperty($property->getName(), true);
    }
    return $arrayData;
  }

  public function getProperty(string $name, bool $toArray = false): mixed
  {
    $key = [__FUNCTION__, $this->getModel()->getModelClassName(), $this->getData()[$name] ?? '0', $name, $toArray];

    return Cache::single($key, function () use ($name, $toArray, $key) {
      $data = $this->getData();
      $property = $this->getModel()->getMeta()->getPropertyWithName($name);
      $value = $data[$name] ?? null;
      return $this->_castDataType($property, $value, false, $toArray);
    });
  }

  public function getData(): array
  {
    return $this->data;
  }

  public function setData(array $data): void
  {
    $this->data = $data;
  }

  public function offsetExists(mixed $offset): bool
  {
    foreach ($this->getModel()->getMeta()->getProperties() as $property) {
      if ($property->getName() == $offset) {
        return true;
      }
    }
    return false;
  }

  public function offsetGet(mixed $offset): mixed
  {
    return $this->__get($offset);
  }

  public function __get(string $name)
  {
    return $this->getProperty($name);
  }

  public function __set(string $name, mixed $value)
  {
    $property = $this->getModel()->getMeta()->getPropertyWithName($name);
    $this->setProperty($property, $value, true);
  }

  public function offsetSet(mixed $offset, mixed $value): void
  {
    $this->__set($offset, $value);
  }

  public function offsetUnset(mixed $offset): void
  {
    $this->__set($offset, null);
  }

  abstract public function getTimestamp(): int;
}
