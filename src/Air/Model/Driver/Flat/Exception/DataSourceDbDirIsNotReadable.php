<?php

namespace Air\Model\Driver\Flat\Exception;

use Exception;

/**
 * Class DataSourceDbDirIsNotReadable
 * @package Air\Model\Driver\Flat\Exception
 */
class DataSourceDbDirIsNotReadable extends Exception
{
  /**
   * DataSourceDbDirIsNotReadable constructor.
   *
   * @param string $dataSourceDir
   * @param array $userInfo
   */
  public function __construct(string $dataSourceDir, array $userInfo)
  {
    parent::__construct("DataSourceDbDirIsNotReadable: " . $dataSourceDir . ', user info is: ' . var_export($userInfo, true));
  }
}