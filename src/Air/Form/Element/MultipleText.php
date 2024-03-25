<?php

declare(strict_types=1);

namespace Air\Form\Element;

class MultipleText extends ElementAbstract
{
  /**
   * @var string|null
   */
  public ?string $elementTemplate = 'form/element/multiple-text';

  /**
   * @return array
   */
  public function getValue(): array
  {
    $value = parent::getValue();

    if ($value === null) {
      return [];
    }

    return $value;
  }
}