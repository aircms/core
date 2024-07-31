<?php

declare(strict_types=1);

namespace Air\Form\Element;

use Air\Core\Exception\ClassWasNotFound;
use Air\Crud\Locale;
use Air\Form\Exception\FilterClassWasNotFound;
use Air\Form\Exception\ValidatorClassWasNotFound;

class KeyValue extends KeyValueAbstract
{
  /**
   * @var string|null
   */
  public ?string $elementTemplate = 'form/element/key-value';

  /**
   * @param $value
   * @return bool
   * @throws FilterClassWasNotFound
   * @throws ValidatorClassWasNotFound
   * @throws ClassWasNotFound
   */
  public function isValid($value): bool
  {
    $isValid = parent::isValid($value);

    if (!$isValid) {
      return false;
    }

    if (!$this->isAllowNull()) {
      $value = $this->getValue();

      if (!strlen($value[$this->getKeyPropertyName()]) || !strlen($value[$this->getValuePropertyName()])) {
        $this->errorMessages[] = Locale::t('Could not be empty');
        return false;
      }
    }

    return true;
  }

  /**
   * @return array|null
   */
  public function getValue(): ?array
  {
    $value = (array)parent::getValue();

    if (!strlen(($value[$this->getKeyPropertyName()]) ?? '') ||
      !strlen(($value[$this->getValuePropertyName()] ?? ''))) {
      return null;
    }

    return $value;
  }
}
