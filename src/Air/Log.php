<?php

declare(strict_types=1);

namespace Air;

use Air\Core\Exception\ClassWasNotFound;
use Air\Core\Front;

class Log
{
  /**
   * @param string $title
   * @param array $data
   * @return int
   * @throws ClassWasNotFound
   * @throws Model\Exception\CallUndefinedMethod
   * @throws Model\Exception\ConfigWasNotProvided
   * @throws Model\Exception\DriverClassDoesNotExists
   * @throws Model\Exception\DriverClassDoesNotExtendsFromDriverAbstract
   */
  public static function info(string $title, array $data = []): int
  {
    return self::write($title, $data);
  }

  /**
   * @param string $title
   * @param array $data
   * @return int
   * @throws ClassWasNotFound
   * @throws Model\Exception\CallUndefinedMethod
   * @throws Model\Exception\ConfigWasNotProvided
   * @throws Model\Exception\DriverClassDoesNotExists
   * @throws Model\Exception\DriverClassDoesNotExtendsFromDriverAbstract
   */
  public static function error(string $title, array $data = []): int
  {
    return self::write($title, $data, Crud\Model\Log::ERROR);
  }

  /**
   * @param string $title
   * @param array $data
   * @param string $level
   * @return int
   * @throws ClassWasNotFound
   * @throws Model\Exception\CallUndefinedMethod
   * @throws Model\Exception\ConfigWasNotProvided
   * @throws Model\Exception\DriverClassDoesNotExists
   * @throws Model\Exception\DriverClassDoesNotExtendsFromDriverAbstract
   */
  public static function write(string $title, array $data = [], string $level = Crud\Model\Log::INFO): int
  {
    $logsEnabled = Front::getInstance()->getConfig()['air']['logs']['enabled'] ?? false;

    if ($logsEnabled) {
      $log = new Crud\Model\Log();

      $log->title = $title;
      $log->data = $data;
      $log->level = $level;
      $log->created = time();

      return $log->save();
    }

    return 0;
  }
}
