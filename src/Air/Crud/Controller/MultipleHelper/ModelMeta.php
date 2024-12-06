<?php

declare(strict_types=1);

namespace Air\Crud\Controller\MultipleHelper;

use Air\Core\Front;
use Air\Model\ModelAbstract;

trait ModelMeta
{
  public ?ModelAbstract $model = null;

  protected function getModelClassName(): string
  {
    $controllerClassPars = explode('\\', get_class($this));

    $entity = end($controllerClassPars);

    return implode('\\', [
      Front::getInstance()->getConfig()['air']['loader']['namespace'],
      'Model',
      $entity
    ]);
  }

  protected function getEntity(): string
  {
    $controllerClassPars = explode('\\', get_class($this));
    return end($controllerClassPars);
  }

  protected function getFormClassName(): string
  {
    $controllerClassPars = explode('\\', get_class($this));
    $controllerClassPars[count($controllerClassPars) - 2] = 'Form';
    return implode('\\', $controllerClassPars);
  }
}