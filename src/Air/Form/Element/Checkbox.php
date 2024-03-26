<?php

namespace Air\Form\Element;

/**
 * Class Checkbox
 * @package Air\Form\Element
 */
class Checkbox extends ElementAbstract
{
  /**
   * @var string|null
   */
  public ?string $elementTemplate = 'form/element/checkbox';

  /**
   * Checkbox constructor.
   *
   * @param string $name
   * @param array $userOptions
   */
  public function __construct(string $name, array $userOptions = [])
  {
    parent::__construct($name, $userOptions);
    $this->setAllowNull(true);
  }

  /**
   * @return bool
   */
  public function getValue(): bool
  {
    return (bool)parent::getValue();
  }
}
