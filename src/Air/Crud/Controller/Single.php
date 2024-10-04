<?php

declare(strict_types=1);

namespace Air\Crud\Controller;

use Air\Core\Exception\ClassWasNotFound;
use Air\Core\Exception\DomainMustBeProvided;
use Air\Form\Exception\FilterClassWasNotFound;
use Air\Form\Exception\ValidatorClassWasNotFound;
use Air\Model\Exception\CallUndefinedMethod;
use Air\Model\Exception\ConfigWasNotProvided;
use Air\Model\Exception\DriverClassDoesNotExists;
use Air\Model\Exception\DriverClassDoesNotExtendsFromDriverAbstract;
use Air\Model\ModelAbstract;
use Exception;

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
   * @throws FilterClassWasNotFound
   * @throws ValidatorClassWasNotFound
   * @throws Exception
   */
  public function index(): ?array
  {
    $this->getRequest()->setGetParam('quick-save', true, true);
    $this->getView()->assign('isSingleControl', true);

    /** @var ModelAbstract $modelClassName */
    $modelClassName = $this->getModelClassName();
    return parent::manage($modelClassName::fetchObject()->id);
  }

  /**
   * @param string|null $id
   * @return void
   * @throws DomainMustBeProvided
   */
  public function manage(string $id = null): void
  {
    $this->redirect($this->getRouter()->assemble(['controller' => $this->getRouter()->getController()], [], true));
  }
}
