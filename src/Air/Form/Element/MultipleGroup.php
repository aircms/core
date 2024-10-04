<?php

declare(strict_types=1);

namespace Air\Form\Element;

use Air\Crud\Locale;
use Air\Form\Exception\FilterClassWasNotFound;
use Air\Form\Exception\ValidatorClassWasNotFound;

class MultipleGroup extends ElementAbstract
{
  /**
   * @var string|null
   */
  public ?string $elementTemplate = 'form/element/multiple-group';

  /**
   * @var array
   */
  public array $elements = [];

  /**
   * @var Group[]
   */
  public array $groups = [];

  /**
   * @var Group
   */
  public Group $group;

  /**
   * @var array|null
   */
  public ?array $defaultValue = [];

  /**
   * @var bool
   */
  public bool $allowNullGroup = true;

  /**
   * @var bool
   */
  public bool $isFixed = false;

  /**
   * @return array|null
   */
  public function getDefaultValue(): ?array
  {
    return $this->defaultValue;
  }

  /**
   * @param array|null $defaultValue
   * @return void
   */
  public function setDefaultValue(?array $defaultValue): void
  {
    $this->defaultValue = $defaultValue;
  }

  /**
   * @return bool
   */
  public function isAllowNullGroup(): bool
  {
    return $this->allowNullGroup;
  }

  /**
   * @param bool $allowNullGroup
   * @return void
   */
  public function setAllowNullGroup(bool $allowNullGroup): void
  {
    $this->allowNullGroup = $allowNullGroup;
  }

  /**
   * @return ElementAbstract[]
   */
  public function getElements(): array
  {
    $cleanElements = [];
    foreach ($this->elements as $element) {
      $cleanElements[] = clone $element;
    }
    return $cleanElements;
  }

  /**
   * @param ElementAbstract[] $elements
   * @return void
   */
  public function setElements(array $elements): void
  {
    $this->elements = $elements;
  }

  /**
   * @return Group[]
   */
  public function getGroups(): array
  {
    return $this->groups;
  }

  /**
   * @param Group[] $groups
   * @return void
   */
  public function setGroups(array $groups): void
  {
    $this->groups = $groups;
  }

  /**
   * @return Group
   */
  public function getGroup(): Group
  {
    return $this->group;
  }

  /**
   * @param Group $group
   * @return void
   */
  public function setGroup(Group $group): void
  {
    $this->group = $group;
  }

  /**
   * @return bool
   */
  public function isFixed(): bool
  {
    return $this->isFixed;
  }

  /**
   * @param bool $isFixed
   */
  public function setIsFixed(bool $isFixed): void
  {
    $this->isFixed = $isFixed;
  }

  /**
   * @return void
   */
  public function init(): void
  {
    parent::init();

    $this->initGroups($this->value);

    $name = $this->getName() . '[{{groupId}}]';

    $this->group = new Group($name, [
      'elements' => $this->getElements(),
      'allowNull' => $this->isAllowNullGroup(),
      'containerTemplate' => 'form/element/multiple-group/group-template',
      'value' => $this->defaultValue,
    ]);

    $this->group->init();
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

    if ((!$value || !count($value)) && $this->isAllowNull()) {
      $this->value = [];
      return true;
    }

    $this->value = array_values(($value ?? []));

    $this->initGroups($this->value);
    $groups = $this->getGroups();

    if (!count($groups) && !$this->isAllowNull()) {
      $this->errorMessages = [Locale::t('Could not be empty')];
      return false;
    }

    foreach ($groups as $index => $group) {
      if (!$group->isValid($this->value[$index])) {
        $isValid = false;
      }
    }

    if (!$isValid) {
      $this->errorMessages = [Locale::t('Could not be empty')];
    }

    return $isValid;
  }

  /**
   * @param array|null $value
   * @return void
   */
  public function initGroups(?array $value = []): void
  {
    $this->groups = [];

    foreach (($value ?? []) as $groupValue) {
      $group = new Group($this->getName() . '[' . uniqid() . ']', [
        'label' => $this->getLabel(),
        'elements' => $this->getElements(),
        'allowNull' => $this->isAllowNullGroup(),
        'containerTemplate' => 'form/element/multiple-group/group-template',
        'value' => $groupValue,
      ]);
      $group->init();
      $this->groups[] = $group;
    }
  }

  /**
   * @return array
   */
  public function getValue(): array
  {
    $value = parent::getValue();

    if (!$value) {
      return [];
    }

    $this->initGroups($value);

    $value = [];

    foreach ($this->getGroups() as $group) {
      $value[] = $group->getValue();
    }

    return $value;
  }

  /**
   * @return mixed
   */
  public function getCleanValue(): mixed
  {
    $value = parent::getCleanValue();

    if (!$value) {
      return [];
    }

    $this->initGroups($value);

    $value = [];

    foreach ($this->getGroups() as $group) {
      $value[] = $group->getCleanValue();
    }

    return $value;
  }
}