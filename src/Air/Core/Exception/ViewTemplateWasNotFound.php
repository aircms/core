<?php

namespace Air\Core\Exception;

use Exception;

/**
 * Class ViewTemplateWasNotFound
 * @package Air\Exception
 */
class ViewTemplateWasNotFound extends Exception
{
  /**
   * ViewTemplateWasNotFound constructor.
   * @param string $template
   */
  public function __construct(string $template)
  {
    parent::__construct('ViewTemplateWasNotFound: ' . $template, 500);
  }
}