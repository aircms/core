<?php

declare(strict_types=1);

namespace Air\Crud\Controller;

use Air\Core\Exception\ClassWasNotFound;
use Air\Core\Exception\DomainMustBeProvided;
use Air\Core\Exception\RouterVarMustBeProvided;
use Air\Model\Exception\CallUndefinedMethod;
use Air\Model\Exception\ConfigWasNotProvided;
use Air\Model\Exception\DriverClassDoesNotExists;
use Air\Model\Exception\DriverClassDoesNotExtendsFromDriverAbstract;
use Air\Model\ModelAbstract;

abstract class Single extends Multiple
{
  /**
   * @return array|null
   * @throws CallUndefinedMethod
   * @throws ClassWasNotFound
   * @throws ConfigWasNotProvided
   * @throws DomainMustBeProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   * @throws RouterVarMustBeProvided
   */
  public function index(): ?array
  {
    $this->getRequest()->setGetParam('quick-save', true, true);
    $this->getView()->assign('isSingleControl', true);

    /** @var ModelAbstract $modelClassName */
    $modelClassName = $this->getModelClassName();
    return $this->manage($modelClassName::fetchObject()->id);
  }
}
