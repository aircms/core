<?php

declare(strict_types=1);

namespace Air\Crud\Controller;

use Air\Crud\Auth;
use Exception;
use Air\Core\Controller;
use Air\Core\Exception\ClassWasNotFound;
use Air\Core\Exception\DomainMustBeProvided;
use Air\Core\Exception\RouterVarMustBeProvided;
use Air\Core\Front;
use Air\Filter\Trim;
use Air\Model\Exception\CallUndefinedMethod;
use Air\Model\Exception\ConfigWasNotProvided;
use Air\Model\Exception\DriverClassDoesNotExists;
use Air\Model\Exception\DriverClassDoesNotExtendsFromDriverAbstract;

class Login extends Controller
{
  /**
   * @return string|array
   * @throws CallUndefinedMethod
   * @throws ClassWasNotFound
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   * @throws Exception
   */
  public function index(): string|array
  {
    if ($this->getRequest()->isPost()) {

      $login = Trim::clean($this->getParam('login'));
      $password = Trim::clean($this->getParam('password'));

      if (Auth::getInstance()->isValid($login, $password)) {
        Auth::getInstance()->authorize($login, $password);
        return [];
      }

      $this->getResponse()->setStatusCode(400);
      return [];
    }

    $this->getView()->assign('title', Front::getInstance()->getConfig()['air']['admin']['title']);
    $this->getView()->setPath(__DIR__ . '/../View');

    return $this->getView()->render('login');
  }

  /**
   * @return void
   * @throws ClassWasNotFound
   * @throws DomainMustBeProvided
   * @throws RouterVarMustBeProvided
   */
  public function logout(): void
  {
    Auth::getInstance()->remove();
    $this->redirect($this->getRouter()->assemble(['action' => 'index']));
  }
}