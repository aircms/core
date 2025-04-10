<?php

declare(strict_types=1);

namespace Air;

use Air\Model\Driver\Mongodb\Cursor;
use Air\Model\ModelAbstract;
use Air\Model\ModelInterface;
use Air\Type\TypeAbstract;
use Closure;
use Throwable;

class Map
{
  public static function execute(mixed $data, mixed $mapper, array $userData = []): mixed
  {
    if (!$data) {
      return null;
    }
    if (is_array($mapper)) {
      return self::executeAssoc($data, $mapper, $userData);
    }
    if (is_string($mapper)) {
      return self::executeLine($data, $mapper, $userData);
    }
    if ($mapper instanceof Closure) {
      return self::executeLineClosure($data, $mapper, $userData);
    }
    return null;
  }

  public static function single(mixed $data, mixed $mapper, array $userData = []): ?array
  {
    return self::execute($data, $mapper, $userData);
  }

  public static function multiple(mixed $data, mixed $mapper, array $userData = []): array
  {
    $result = self::execute($data, $mapper, $userData);
    return $result ?: [];
  }

  public static function isSingle(mixed $data): bool
  {
    return $data instanceof ModelInterface || (!isset($data[0]) && !($data instanceof Cursor));
  }

  public static function executeAssoc(mixed $data, array $mapper = [], array $userData = []): ?array
  {
    if (self::isSingle($data)) {
      return self::executeSingle($data, $mapper, $userData);
    }
    $mapped = [];
    foreach ($data as $row) {
      $mapped[] = self::executeSingle($row, $mapper, $userData);
    }
    return $mapped;
  }

  public static function executeLine(mixed $data, string $mapper = null, array $userData = []): mixed
  {
    if (self::isSingle($data)) {
      return self::executeSingle($data, [$mapper], $userData)[$mapper];
    }
    $mapped = [];
    foreach ($data as $row) {
      $mapped[] = self::executeSingle($row, [$mapper], $userData)[$mapper];
    }
    return $mapped;
  }

  public static function executeLineClosure(mixed $data, Closure $mapper, array $userData = null): mixed
  {
    if (self::isSingle($data)) {
      return self::transform($data, null, $mapper, $userData);
    }
    $mapped = [];
    foreach ($data as $row) {
      $mapped[] = self::transform($row, null, $mapper, $userData);
    }
    return $mapped;
  }

  public static function executeSingle($data, array $mapper, array $userData = null): array
  {
    $mapped = [];
    foreach ($mapper as $dest => $value) {
      if (is_string($dest)) {
        $mapped[$dest] = self::transform($data, $dest, $value, $userData ?? []);
      } else {
        $mapped[$value] = self::transform($data, $dest, $value, $userData ?? []);
      }
    }
    return $mapped;
  }

  public static function transform($data, $dest, $value, array $userData = null): mixed
  {
    if ($value instanceof Closure) {
      return $value($data, $userData ?: []);
    }
    if ($data instanceof ModelAbstract) {
      try {
        return $data->getMeta()->hasProperty($value) ? $data->{$value} : $data->{$dest};
      } catch (Throwable) {
        return null;
      }
    }
    if ($data instanceof TypeAbstract) {
      try {
        return $data->{$value} ?? $data->{$data};
      } catch (Throwable) {
        return null;
      }
    }
    if (is_array($data)) {
      return $data[$value] ?? $data[$dest] ?? null;
    }
    return null;
  }
}
