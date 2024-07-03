<?php

declare(strict_types=1);

namespace Air\Form\Element;

use Air\Crud\Locale;
use Air\Form\Exception\FilterClassWasNotFound;
use Air\Form\Exception\ValidatorClassWasNotFound;
use Air\Model\ModelAbstract;

class MultipleModel extends ElementAbstract
{
  /**
   * @var string|null
   */
  public ?string $elementTemplate = 'form/element/multiple-model';

  /**
   * @var string
   */
  public string $model = '';

  /**
   * @var string
   */
  public string $field = 'title';

  /**
   * @return string
   */
  public function getField(): string
  {
    return $this->field;
  }

  /**
   * @param string $field
   * @return void
   */
  public function setField(string $field): void
  {
    $this->field = $field;
  }

  /**
   * @return ModelAbstract
   */
  public function getModel(): ModelAbstract
  {
    return new $this->model;
  }

  /**
   * @param string $model
   * @return void
   */
  public function setModel(string $model): void
  {
    $this->model = $model;
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

    if (!$this->isAllowNull()) {
      $value = $this->getValue();
      $count = !!count($value);

      if (!$count) {
        $this->errorMessages[] = Locale::t('Could not be empty');
        return false;
      }
      return true;
    }

    return $isValid;
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

    if (is_array($value)) {
      return $value;
    }

    if (is_string($value)) {
      $value = json_decode($value, true);
    }

    return $value;
  }

  /**
   * @return array
   */
  public function getRawValue(): array
  {
    $ids = [];
    foreach ($this->getValue() as $item) {
      if ($item instanceof ModelAbstract) {
        $ids[] = (string)$item->id;

      } elseif (is_string($item)) {
        $ids[] = $item;
      }
    }
    return $ids;
  }

  /**
   * @return mixed
   */
  public function getCleanValue(): mixed
  {
    return $this->getRawValue();
  }
}
