<?php

namespace Air\Form\Exception;

use Exception;

/**
 * Class FilterClassWasNotFound
 * @package Air\Form\Exception
 */
class FilterClassWasNotFound extends Exception
{
  /**
   * FilterClassWasNotFound constructor.
   * @param string|null $filterClassName
   */
  public function __construct(string $filterClassName = null)
  {
    parent::__construct('FilterClassWasNotFound: ' . $filterClassName);
  }
}
