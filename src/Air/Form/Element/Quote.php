<?php

declare(strict_types=1);

namespace Air\Form\Element;

class Quote extends ElementAbstract
{
  /**
   * @var string|null
   */
  public ?string $elementTemplate = 'form/element/quote';

  /**
   * @return \Air\Type\Quote|null
   */
  public function getValue(): ?\Air\Type\Quote
  {
    $value = parent::getValue();

    if (!$value || (!strlen($value['quote']) && !strlen($value['author']))) {
      return null;
    }

    return new \Air\Type\Quote((array)$value);
  }
}