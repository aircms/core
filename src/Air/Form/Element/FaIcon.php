<?php

declare(strict_types=1);

namespace Air\Form\Element;

use Throwable;

class FaIcon extends ElementAbstract
{
  public ?string $elementTemplate = 'form/element/fa-icon';

  public function getValue(): ?\Air\Type\FaIcon
  {
    $value = parent::getValue();

    if (is_array($value)) {
      $value = new \Air\Type\FaIcon($value);

    } else if (is_string($value)) {
      try {
        $value = json_decode($value, true);
      } catch (Throwable) {
      }

    } else if ($value instanceof \Air\Type\FaIcon) {
      return $value;
    }

    if (isset($value['icon']) && isset($value['style'])) {
      return new \Air\Type\FaIcon([
        'icon' => (string)$value['icon'],
        'style' => (string)$value['style'],
      ]);
    }

    return null;
  }

  public function getCleanValue(): mixed
  {
    return $this->getValue()?->toArray();
  }
}