<?php

namespace Air\Form\Exception;

use Exception;

/**
 * Class ValidatorClassWasNotFound
 * @package Air\Form\Exception
 */
class ValidatorClassWasNotFound extends Exception
{
  /**
   * ValidatorClassWasNotFound constructor.
   * @param string|null $validatorClassName
   */
  public function __construct(string $validatorClassName = null)
  {
    parent::__construct('ValidatorClassWasNotFound: ' . $validatorClassName);
  }
}