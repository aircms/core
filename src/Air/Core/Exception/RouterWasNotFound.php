<?php

namespace Air\Core\Exception;

use Exception;

/**
 * Class RouterWasNotFound
 * @package Air\Exception
 */
class RouterWasNotFound extends Exception
{
  /**
   * RouterWasNotFound constructor.
   * @param string $router
   */
  public function __construct(string $router)
  {
    parent::__construct('RouterWasNotFound: ' . $router, 404);
  }
}