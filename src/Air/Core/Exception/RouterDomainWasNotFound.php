<?php

namespace Air\Core\Exception;

use Exception;

/**
 * Class ActionMethodWasNotFound
 * @package Air\Exception
 */
class RouterDomainWasNotFound extends Exception
{
  /**
   * RouterDomainWasNotFound constructor.
   * @param string $domain
   */
  public function __construct(string $domain)
  {
    parent::__construct('RouterDomainWasNotFound: ' . $domain, 404);
  }
}