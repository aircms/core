<?php

declare(strict_types=1);

namespace Air;

use Air\Crud\Log\Model;

class Log
{
  /**
   * @param string $title
   * @param array $data
   * @return int
   */
  public static function info(string $title, array $data = []): int
  {
    return self::write($title, $data);
  }

  /**
   * @param string $title
   * @param array $data
   * @return int
   */
  public static function error(string $title, array $data = []): int
  {
    return self::write($title, $data, Model::ERROR);
  }

  /**
   * @param string $title
   * @param array $data
   * @param string $level
   * @return int
   */
  public static function write(string $title, array $data = [], string $level = Model::INFO): int
  {
    $log = new Model();

    $log->title = $title;
    $log->data = $data;
    $log->level = $level;
    $log->created = time();

    return $log->save();
  }
}
