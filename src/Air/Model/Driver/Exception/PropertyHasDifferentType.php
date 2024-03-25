<?php

declare(strict_types=1);

namespace Air\Model\Driver\Exception;

use Exception;

class PropertyHasDifferentType extends Exception
{
  /**
   * @param string $className
   * @param string $propertyName
   * @param string $validPropertyType
   * @param string $invalidPropertyType
   */
  public function __construct(string $className, string $propertyName, string $validPropertyType, string $invalidPropertyType)
  {
    parent::__construct("PropertyHasDifferentType: " . $className . '::' . $propertyName . ' trying to set value with type ' . $invalidPropertyType . ' instead of valid type ' . $validPropertyType);
  }
}