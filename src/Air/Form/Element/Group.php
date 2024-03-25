<?php

declare(strict_types=1);

namespace Air\Form\Element;

use Air\Form\Exception\FilterClassWasNotFound;
use Air\Form\Exception\ValidatorClassWasNotFound;

class Group extends ElementAbstract
{
  /**
   * @var string|null
   */
  public ?string $elementTemplate = 'form/element/group';

  /**
   * @var ElementAbstract[]
   */
  public array $elements = [];

  /**
   * @var string[]
   */
  public array $originalElementNames = [];

  /**
   * @return void
   */
  public function init(): void
  {
    parent::init();

    foreach ($this->getElements() as $index => $element) {
      $element->setValue($this->value[$this->originalElementNames[$index]] ?? null);

      $element->setContainerTemplate('form/element/group/container');
      $element->setAllowNull($this->isAllowNull());
    }

    $this->updateNamesWith($this->getName());
  }

  /**
   * @param string $parentName
   * @return void
   */
  public function updateNamesWith(string $parentName): void
  {
    foreach ($this->getElements() as $index => $element) {
      $name = $parentName . '[' . $this->originalElementNames[$index] . ']';

      if ($element instanceof $this) {
        $element->updateNamesWith($name);

      } else {
        $element->setName($name);
      }

      $element->init();
    }
  }

  /**
   * @return ElementAbstract[]
   */
  public function getElements(): array
  {
    return $this->elements;
  }

  /**
   * @param ElementAbstract[] $elements
   * @return void
   */
  public function setElements(array $elements): void
  {
    foreach ($elements as $element) {
      $this->originalElementNames[] = $element->getName();
    }
    $this->elements = $elements;
  }

  /**
   * @param $value
   * @return bool
   * @throws FilterClassWasNotFound
   * @throws ValidatorClassWasNotFound
   */
  public function isValid($value): bool
  {
    $isValid = true;

    foreach ($this->getElements() as $index => $element) {
      $_value = $value[$this->originalElementNames[$index]] ?? null;
      if (!$element->isValid($_value)) {
        $isValid = false;
      }
    }

    return $isValid;
  }

  /**
   * @return bool
   */
  public function hasError(): bool
  {
    $hasError = false;

    foreach ($this->getElements() as $element) {
      if ($element->hasError()) {
        $hasError = true;
      }
    }

    return $hasError;
  }

  /**
   * @return array
   */
  public function getValue(): array
  {
    $value = [];

    foreach ($this->getElements() as $index => $element) {
      $value[$this->originalElementNames[$index]] = $element->getValue();
    }

    if (!count($value)) {
      return [];
    }

    return $value;
  }
}