<?php

declare(strict_types=1);

namespace Air;

use Air\Core\Front;
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
    $logsEnabled = Front::getInstance()->getConfig()['ait']['admin']['logs']['enabled'] ?? false;

    if ($logsEnabled) {
      $log = new Model();

      $log->title = $title;
      $log->data = $data;
      $log->level = $level;
      $log->created = time();

      return $log->save();
    }

    return 0;
  }
}
