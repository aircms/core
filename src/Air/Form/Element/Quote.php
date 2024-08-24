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

class Quote extends ElementAbstract
{
  /**
   * @var string|null
   */
  public ?string $elementTemplate = 'form/element/quote';

  /**
   * @return \Air\Type\Quote|null
   * @throws ClassWasNotFound
   * @throws CallUndefinedMethod
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   * @throws ReflectionException
   * @throws Throwable
   */
  public function getValue(): ?\Air\Type\Quote
  {
    $value = parent::getValue();

    if (!$value || (!strlen($value['quote']) && !strlen($value['author']))) {
      return null;
    }

    return new \Air\Type\Quote((array)$value);
  }
}