<?php

namespace Air\Model\Driver\Flat\Exception;

use Exception;

/**
 * Class DataSourceFileIsNotReadable
 * @package Air\Model\Driver\Flat\Exception
 */
class DataSourceFileIsNotReadable extends Exception
{
  /**
   * DataSourceFileIsNotReadable constructor.
   *
   * @param string $dataSourceDir
   * @param array $userInfo
   */
  public function __construct(string $dataSourceDir, array $userInfo)
  {
    parent::__construct("DataSourceFileIsNotReadable: " . $dataSourceDir . ', user info is: ' . var_export($userInfo, true));
  }
}