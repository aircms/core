<?php

declare(strict_types=1);

namespace Air\Form\Element;

abstract class TextAbstract extends ElementAbstract
{
  /**
   * @return string
   */
  public function getValue(): string
  {
    $value = parent::getValue();

    if ($value === null) {
      return '';
    }

    return $value;
  }
}