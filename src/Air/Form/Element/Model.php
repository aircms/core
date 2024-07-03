<?php

declare(strict_types=1);

namespace Air\Form\Element;

use Air\Core\Exception\ClassWasNotFound;
use Air\Crud\Locale;
use Air\Model\Exception\CallUndefinedMethod;
use Air\Model\Exception\ConfigWasNotProvided;
use Air\Model\Exception\DriverClassDoesNotExists;
use Air\Model\Exception\DriverClassDoesNotExtendsFromDriverAbstract;
use Air\Model\ModelAbstract;

class Model extends ElementAbstract
{
  /**
   * @var string|null
   */
  public ?string $elementTemplate = 'form/element/model';

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
   * @return ModelAbstract|null
   * @throws ClassWasNotFound
   * @throws CallUndefinedMethod
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   */
  public function getValue(): ?ModelAbstract
  {
    $value = parent::getValue();

    if (!$value) {
      return null;
    }

    if ($value instanceof ModelAbstract) {
      return $value;
    }

    /** @var ModelAbstract $model */
    $model = $this->model;

    return $model::fetchObject(['id' => $value]);
  }

  /**
   * @return mixed
   */
  public function getCleanValue(): mixed
  {
    $value = parent::getCleanValue();

    if (!$value) {
      return null;
    }

    if ($value instanceof ModelAbstract) {
      return $value->id;
    }

    return $value;
  }
}
