<?php

declare(strict_types=1);

namespace Air\Model\Meta\Exception;

use Exception;

class PropertyWasNotFound extends Exception
{
  /**
   * @param string $collection
   * @param string $property
   */
  public function __construct(string $collection, string $property)
  {
    parent::__construct("PropertyWasNotFound: collection: '" . $collection . "', property: " . $property);
  }
}