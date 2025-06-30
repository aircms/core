<?php

declare(strict_types=1);

namespace Air\Model;

use Air\Model\Driver\CursorAbstract;
use Air\Model\Driver\DocumentAbstract;

interface ModelInterface
{
  public static function fetchAll(
    array $cond = [],
    array $sort = [],
    ?int  $count = null,
    ?int  $offset = null
  ): CursorAbstract|array;

  public static function fetchOne(array $cond = [], array $sort = []): DocumentAbstract|static|null;

  public static function fetchObject(array $cond = [], array $sort = []): DocumentAbstract|static|null;

  public static function count(array $cond = []): int;

  public static function batchInsert(array $data = []): int;

  public static function insert(array $data = []): int;

  public static function update(array $cond = [], array $data = []): int;

  public function getData(): array;

  public function populate(array $data): void;

  public function save(): int;

  public function toArray(): array;
}