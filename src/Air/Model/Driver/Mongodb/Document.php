<?php

declare(strict_types=1);

namespace Air\Model\Driver\Mongodb;

use Air\Model\Driver\DocumentAbstract;
use MongoDB\BSON\ObjectID;

class Document extends DocumentAbstract
{
  public function getTimestamp(): int
  {
    $primaryValue = $this->getModel()->{$this->getModel()->getMeta()->getPrimary()};
    if (!$primaryValue) {
      return 0;
    }
    $objectId = new ObjectID($primaryValue);
    return $objectId->getTimestamp();
  }
}