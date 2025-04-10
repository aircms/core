<?php

declare(strict_types=1);

namespace Air\Form\Exception;

use Exception;

class ValidatorClassWasNotFound extends Exception
{
  public function __construct(string $validatorClassName = null)
  {
    parent::__construct($validatorClassName);
  }
}