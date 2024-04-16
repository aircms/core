<?php

declare(strict_types=1);

namespace Air\Form\Element;

use Air\Core\Exception\ClassWasNotFound;
use Air\Model\Exception\CallUndefinedMethod;
use Air\Model\Exception\ConfigWasNotProvided;
use Air\Model\Exception\DriverClassDoesNotExists;
use Air\Model\Exception\DriverClassDoesNotExtendsFromDriverAbstract;
use Air\Model\ModelAbstract;

class TreeModel extends ElementAbstract
{
  /**
   * @var string|null
   */
  public ?string $elementTemplate = 'form/element/tree-model';

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
   * @param ModelAbstract $model
   * @param array $ids
   * @return array
   */
  public static function getModelChain(ModelAbstract $model, array $ids = []): array
  {
    $ids[] = (string)$model->id;

    if ($model->getMeta()->hasProperty('parent') && $model->parent && $model->parent instanceof ModelAbstract) {
      return self::getModelChain($model->parent, $ids);
    }

    return $ids;
  }

  /**
   * @return array|ModelAbstract[]
   * @throws CallUndefinedMethod
   * @throws ClassWasNotFound
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   */
  public function getValue(): array
  {
    $value = parent::getValue();

    if (!$value) {
      return [];
    }

    if (is_string($value)) {
      $value = self::getModelChain($this->getModel()::fetchOne(['id' => $value]));
    }

    return $value;
  }
}
