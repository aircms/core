<?php

declare(strict_types=1);

namespace Air;

use Air\Core\Front;

class Log
{
  public static function info(string $title, array $data = []): int
  {
    return self::write($title, $data);
  }

  public static function error(string $title, array $data = []): int
  {
    return self::write($title, $data, Crud\Model\Log::ERROR);
  }

  public static function write(string $title, array $data = [], string $level = Crud\Model\Log::INFO): int
  {
    $logsEnabled = Front::getInstance()->getConfig()['air']['logs']['enabled'] ?? false;

    if ($logsEnabled) {
      $log = new Crud\Model\Log();

      $log->title = $title;
      $log->data = $data;
      $log->level = $level;

      return $log->save();
    }

    return 0;
  }
}
