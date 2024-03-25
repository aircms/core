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
   * @param array $options
   */
  public function __construct(string $name, array $options = [])
  {
    $this->setAllowNull(true);
    parent::__construct($name, $options);
  }

  /**
   * @return bool
   */
  public function getValue(): bool
  {
    return (bool)parent::getValue();
  }
}
