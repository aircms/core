<?php

declare(strict_types=1);

namespace Air\Crud\Controller\MultipleHelper;

use Air\Core\Front;
use Air\Model\ModelAbstract;
use ReflectionClass;

trait ModelMeta
{
  public ?ModelAbstract $model = null;

  protected function getModelClassName(): string
  {
    return implode('\\', [
      Front::getInstance()->getConfig()['air']['loader']['namespace'],
      'Model',
      $this->getEntity()
    ]);
  }

  protected function getEntity(): string
  {
    $reflectionClass = new ReflectionClass($this);
    return $reflectionClass->getShortName();
  }

  protected function getFormClassName(): string
  {
    $controllerClassPars = explode('\\', get_class($this));
    $controllerClassPars[count($controllerClassPars) - 2] = 'Form';
    return implode('\\', $controllerClassPars);
  }

  protected function getModelClass(): ModelAbstract
  {
    /** @var ModelAbstract $modelClassName */
    $modelClassName = $this->getModelClassName();
    return new $modelClassName();
  }
}