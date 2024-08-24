<?php

declare(strict_types=1);

namespace Air\Form\Element;

use Air\Core\Exception\ClassWasNotFound;
use Air\Model\Exception\CallUndefinedMethod;
use Air\Model\Exception\ConfigWasNotProvided;
use Air\Model\Exception\DriverClassDoesNotExists;
use Air\Model\Exception\DriverClassDoesNotExtendsFromDriverAbstract;
use ReflectionException;
use Throwable;

class FaIcon extends ElementAbstract
{
  /**
   * @var string|null
   */
  public ?string $elementTemplate = 'form/element/fa-icon';

  /**
   * @return \Air\Type\FaIcon|null
   * @throws CallUndefinedMethod
   * @throws ClassWasNotFound
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   * @throws ReflectionException
   * @throws Throwable
   */
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

  /**
   * @return mixed
   * @throws CallUndefinedMethod
   * @throws ClassWasNotFound
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   * @throws ReflectionException
   * @throws Throwable
   */
  public function getCleanValue(): mixed
  {
    $value = $this->getValue();
    return $value?->toArray();
  }
}