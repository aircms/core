<?php

declare(strict_types=1);

namespace Air\Form\Element;

class Group extends ElementAbstract
{
  public ?string $elementTemplate = 'form/element/group';
  public array $elements = [];
  public array $originalElementNames = [];

  public function init(): void
  {
    parent::init();

    foreach ($this->getElements() as $index => $element) {
      $element->setValue(((array)$this->value)[$this->originalElementNames[$index]] ?? null);

      $element->setContainerTemplate('form/element/group/container');
      $element->setAllowNull($this->isAllowNull());
    }

    $this->updateNamesWith($this->getName());
  }

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

  public function getElements(): array
  {
    return $this->elements;
  }

  public function setElements(array $elements): void
  {
    foreach ($elements as $element) {
      $this->originalElementNames[] = $element->getName();
    }
    $this->elements = $elements;
  }

  public function isValid($value): bool
  {
    /** @var ElementAbstract $element */

    $isValid = true;

    foreach ($this->getElements() as $index => $element) {
      $_value = $value[$this->originalElementNames[$index]] ?? null;
      if (!$element->isValid($_value)) {
        $this->errorMessages[$this->originalElementNames[$index]] = $element->getErrorMessages();
        $isValid = false;
      }
    }

    return $isValid;
  }

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

  public function getCleanValue(): mixed
  {
    $value = [];

    foreach ($this->getElements() as $index => $element) {
      $value[$this->originalElementNames[$index]] = $element->getCleanValue();
    }

    if (!count($value)) {
      return [];
    }

    return $value;
  }
}