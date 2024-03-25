<?php

declare(strict_types=1);

namespace Air\Model;

use Air\Model\Driver\CursorAbstract;
use Air\Model\Driver\DocumentAbstract;

interface ModelInterface
{
  /**
   * @param array $cond
   * @param array $sort
   * @param int|null $count
   * @param int|null $offset
   *
   * @return CursorAbstract|array|static[]
   */
  public static function fetchAll(
    array $cond = [],
    array $sort = [],
    int   $count = null,
    int   $offset = null
  ): CursorAbstract|array;

  /**
   * @param array $cond
   * @param array $sort
   * @return DocumentAbstract|static|null
   */
  public static function fetchOne(array $cond = [], array $sort = []): DocumentAbstract|static|null;

  /**
   * @param array $cond
   * @param array $sort
   * @return DocumentAbstract|static|null
   */
  public static function fetchObject(array $cond = [], array $sort = []): DocumentAbstract|static|null;

  /**
   * @param array $cond
   * @return int
   */
  public static function count(array $cond = []): int;

  /**
   * @param array $data
   * @return int
   */
  public static function batchInsert(array $data = []): int;

  /**
   * @param array $cond
   * @param array $data
   * @return int
   */
  public static function update(array $cond = [], array $data = []): int;

  /**
   * @return array
   */
  public function getData(): array;

  /**
   * @param array $data
   * @return void
   */
  public function populate(array $data): void;

  /**
   * @return int
   */
  public function save(): int;

  /**
   * @return array
   */
  public function toArray(): array;
}