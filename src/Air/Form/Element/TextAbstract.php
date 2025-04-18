<?php

declare(strict_types=1);

namespace Air\Form\Element;

abstract class TextAbstract extends ElementAbstract
{
  public function getValue(): mixed
  {
    $value = parent::getValue();
    if ($value === null) {
      return '';
    }
    return $value;
  }
}