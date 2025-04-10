<?php

declare(strict_types=1);

namespace Air\Form\Element;

class Checkbox extends ElementAbstract
{
  public ?string $elementTemplate = 'form/element/checkbox';

  public function __construct(string $name, array $userOptions = [])
  {
    parent::__construct($name, $userOptions);
    $this->setAllowNull(true);
  }

  public function getValue(): bool
  {
    return (bool)parent::getValue();
  }
}
