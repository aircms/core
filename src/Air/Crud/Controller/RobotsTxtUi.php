<?php

declare(strict_types=1);

namespace Air\Crud\Controller;

use Air\Core\Controller;
use Air\Core\Exception\ClassWasNotFound;
use Air\Model\Exception\CallUndefinedMethod;
use Air\Model\Exception\ConfigWasNotProvided;
use Air\Model\Exception\DriverClassDoesNotExists;
use Air\Model\Exception\DriverClassDoesNotExtendsFromDriverAbstract;

class RobotsTxtUi extends Controller
{
  /**
   * @return string
   * @throws ClassWasNotFound
   * @throws CallUndefinedMethod
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   */
  public function index(): string
  {
    $this->getResponse()->setHeader('Content-Type', 'text/plain');
    return \Air\Crud\Model\RobotsTxt::one()->description;
  }
}
