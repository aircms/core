<?php

declare(strict_types=1);

namespace Air\Crud\Model;

use Air\Model\ModelAbstract;

/**
 * @collection EmailSettings
 *
 * @property string $id
 *
 * @property string $server
 * @property string $port
 * @property string $protocol
 * @property string $name
 * @property string $address
 * @property string $password
 * @property string $from
 *
 * @property boolean $emailQueueEnabled
 */
class EmailSettings extends ModelAbstract
{
  const string TSL = 'tls';
  const string SSL = 'ssl';
}