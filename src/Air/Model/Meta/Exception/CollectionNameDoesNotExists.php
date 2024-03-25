<?php

declare(strict_types=1);

namespace Air\Model\Meta\Exception;

use Exception;
use Air\Model\ModelAbstract;

class CollectionNameDoesNotExists extends Exception
{
  /**
   * @param ModelAbstract $model
   */
  public function __construct(ModelAbstract $model)
  {
    parent::__construct("CollectionNameDoesNotExists: " . var_export($model, true));
  }
}