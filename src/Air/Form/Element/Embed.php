<?php

declare(strict_types=1);

namespace Air\Form\Element;

class Embed extends ElementAbstract
{
  /**
   * @var string|null
   */
  public ?string $elementTemplate = 'form/element/embed';

  /**
   * @return string|null
   */
  public function getValue(): ?string
  {
    $value = parent::getValue();

    if (!$value || !strlen($value)) {
      return null;
    }

    return $value;
  }
}