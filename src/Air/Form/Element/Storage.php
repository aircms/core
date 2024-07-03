<?php

declare(strict_types=1);

namespace Air\Form\Element;

use Air\Core\Exception\ClassWasNotFound;
use Air\Core\Front;
use Air\Crud\Locale;
use Air\Form\Exception\FilterClassWasNotFound;
use Air\Form\Exception\ValidatorClassWasNotFound;
use Air\Type\File;

class Storage extends ElementAbstract
{
  /**
   * @var bool
   */
  public bool $multiple = false;

  /**
   * @var string|null
   */
  public ?string $elementTemplate = 'form/element/storage';

  /**
   * @var array
   */
  public array $storageSettings;

  /**
   * @return bool
   */
  public function isMultiple(): bool
  {
    return $this->multiple;
  }

  /**
   * @param bool $multiple
   * @return void
   */
  public function setMultiple(bool $multiple): void
  {
    $this->multiple = $multiple;
  }

  /**
   * @return array
   */
  public function getStorageSettings(): array
  {
    return $this->storageSettings;
  }

  /**
   * @param array $storageSettings
   * @return void
   */
  public function setStorageSettings(array $storageSettings): void
  {
    $this->storageSettings = $storageSettings;
  }

  /**
   * @return void
   * @throws ClassWasNotFound
   */
  public function init(): void
  {
    parent::init();
    $this->storageSettings = Front::getInstance()->getConfig()['air']['storage'];
  }

  /**
   * @param $value
   * @return bool
   * @throws FilterClassWasNotFound
   * @throws ValidatorClassWasNotFound
   */
  public function isValid($value): bool
  {
    $isValid = parent::isValid($value);

    if (!$isValid) {
      $this->errorMessages = [Locale::t('Could not be empty')];
      return false;
    }

    if (is_string($value)) {
      $value = json_decode($value, true);
    }

    if ((!is_array($value) || !count($value)) && !$this->isAllowNull()) {
      $this->errorMessages = [Locale::t('Could not be empty')];
      return false;
    }

    return true;
  }

  /**
   * @return File|File[]|null
   */
  public function getValue(): mixed
  {
    $value = parent::getValue();

    if (is_string($value)) {
      $value = json_decode($value, true);
    }

    if (!$value) {
      return $this->isMultiple() ? [] : null;
    }

    return $value;
  }
}