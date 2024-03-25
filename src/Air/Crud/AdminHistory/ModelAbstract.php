<?php

namespace Air\Crud\AdminHistory;

/**
 * @collection AirAdminHistory
 *
 * @property string $id
 * @property array $admin
 * @property integer $dateTime
 * @property string $type
 * @property string $section
 * @property array $entity
 * @property array $was
 * @property array $became
 *
 * @property string $search
 */
class ModelAbstract extends \Air\Model\ModelAbstract
{
  const TYPE_READ_TABLE = 'read-table';
  const TYPE_READ_ENTITY = 'read-entity';
  const TYPE_CREATE_ENTITY = 'create-entity';
  const TYPE_WRITE_ENTITY = 'write-entity';
}