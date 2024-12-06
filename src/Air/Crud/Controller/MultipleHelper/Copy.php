<?php

declare(strict_types=1);

namespace Air\Crud\Controller\MultipleHelper;

use Air\Model\ModelAbstract;

trait Copy
{
  public function copy(string $id): void
  {
    $this->getView()->setLayoutEnabled(false);
    $this->getView()->setAutoRender(false);

    /** @var ModelAbstract $modelClassName */
    $modelClassName = $this->getModelClassName();
    $record = $modelClassName::fetchOne(['id' => $id]);

    $data = $record->getData();
    unset($data['id']);

    if ($record->getMeta()->hasProperty('enabled')) {
      $data['enabled'] = false;
    }

    $newRecord = new $modelClassName();
    $newRecord->populateWithoutQuerying($data);
    $newRecord->save();

    $this->didCopied($record, $newRecord);
  }
}