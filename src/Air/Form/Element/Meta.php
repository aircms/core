<?php

declare(strict_types=1);

namespace Air\Form\Element;

use Air\Core\Exception\ClassWasNotFound;
use Air\Crud\Locale;
use Air\Form\Exception\FilterClassWasNotFound;
use Air\Form\Exception\ValidatorClassWasNotFound;
use Air\Model\Exception\CallUndefinedMethod;
use Air\Model\Exception\ConfigWasNotProvided;
use Air\Model\Exception\DriverClassDoesNotExists;
use Air\Model\Exception\DriverClassDoesNotExtendsFromDriverAbstract;
use Air\Model\Meta\Exception\PropertyWasNotFound;

class Meta extends ElementAbstract
{
  /**
   * @var string|null
   */
  public ?string $elementTemplate = 'form/element/meta';

  /**
   * @param $value
   * @return bool
   * @throws ClassWasNotFound
   * @throws FilterClassWasNotFound
   * @throws ValidatorClassWasNotFound
   */
  public function isValid($value): bool
  {
    $isValid = parent::isValid($value);

    if (!$this->isAllowNull()) {
      if (strlen(trim(implode('', array_filter($value)))) === 0) {
        $this->errorMessages = [Locale::t('Could not be empty')];
        return false;
      }
    }

    return $isValid;
  }

  /**
   * @return \Air\Type\Meta|null
   * @throws ClassWasNotFound
   * @throws CallUndefinedMethod
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   * @throws PropertyWasNotFound
   */
  public function getValue(): ?\Air\Type\Meta
  {
    $value = (array)parent::getValue();

    if (isset($value['ogImage']) && is_string($value['ogImage'])) {
      $value['ogImage'] = json_decode($value['ogImage'], true);
    }

    $value['useModelData'] = (bool)($value['useModelData'] ?? false);

    return new \Air\Type\Meta($value);
  }
}