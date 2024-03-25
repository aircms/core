<?php

declare(strict_types=1);

namespace Air\Form\Element;

class Select extends ElementAbstract
{
  /**
   * @var string|null
   */
  public ?string $elementTemplate = 'form/element/select';

  /**
   * @var array
   */
  public array $options = [];

  /**
   * @return array
   */
  public function getOptions(): array
  {
    return $this->options;
  }

  /**
   * @param array $options
   * @return void
   */
  public function setOptions(array $options): void
  {
    $this->options = $options;
  }

  /**
   * @return mixed
   */
  public function getValue(): mixed
  {
    $value = parent::getValue();

    if (!$value || !strlen($value)) {
      return null;
    }

    return $value;
  }
}
