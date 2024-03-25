<?php

declare(strict_types=1);

namespace Air\Form\Element;

class MultipleKeyValue extends KeyValueAbstract
{
  /**
   * @var string|null
   */
  public ?string $elementTemplate = 'form/element/multiple-key-value';

  /**
   * @return array
   */
  public function getValue(): array
  {
    $value = parent::getValue();

    if ($value === null) {
      return [];
    }

    return array_values($value);
  }
}