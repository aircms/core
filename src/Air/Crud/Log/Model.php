<?php

declare(strict_types=1);

namespace Air\Crud\Log;

use Air\Model\ModelAbstract;

/**
 * @collection AirLogs
 *
 * @property string $id
 *
 * @property string $title
 * @property array $data
 * @property string $level
 * @property integer $created
 */
class Model extends ModelAbstract
{
  const INFO = 'info';
  const ERROR = 'error';
}