<?php

declare(strict_types=1);

namespace Air\Form\Element;

class Text extends TextAbstract
{
  public ?string $type = 'text';

  /**
   * @var string|null
   */
  public ?string $elementTemplate = 'form/element/text';

  /**
   * @return string|null
   */
  public function getType(): ?string
  {
    return $this->type;
  }

  /**
   * @param string|null $type
   */
  public function setType(?string $type): void
  {
    $this->type = $type;
  }

  /**
   * @return mixed
   */
  public function getValue(): mixed
  {
    if ($this->type === 'number') {
      return intval(parent::getValue());
    }
    return parent::getValue();
  }
}