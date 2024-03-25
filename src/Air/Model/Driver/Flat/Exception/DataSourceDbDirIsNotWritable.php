<?php

namespace Air\Model\Driver\Flat\Exception;

use Exception;

/**
 * Class DataSourceDbDirIsNotWritable
 * @package Air\Model\Driver\Flat\Exception
 */
class DataSourceDbDirIsNotWritable extends Exception
{
  /**
   * DataSourceDbDirIsNotWritable constructor.
   *
   * @param string $dataSourceDir
   * @param array $userInfo
   */
  public function __construct(string $dataSourceDir, array $userInfo)
  {
    parent::__construct("DataSourceDbDirIsNotWritable: " . $dataSourceDir . ', user info is: ' . var_export($userInfo, true));
  }
}