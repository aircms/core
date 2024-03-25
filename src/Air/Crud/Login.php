<?php

declare(strict_types=1);

namespace Air\Crud;

use Exception;
use Air\Core\Controller;
use Air\Core\Exception\ClassWasNotFound;
use Air\Core\Exception\DomainMustBeProvided;
use Air\Core\Exception\RouterVarMustBeProvided;
use Air\Core\Front;
use Air\Crud\Admin\ModelAbstract;
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
   * @throws DomainMustBeProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   * @throws RouterVarMustBeProvided
   * @throws Exception
   */
  public function index(): string|array
  {
    if ($this->getRequest()->isPost()) {

      $login = (new Trim())->filter($this->getRequest()->getPost('login'));
      $password = (new Trim())->filter($this->getRequest()->getPost('password'));

      $config = Front::getInstance()->getConfig()['air']['admin']['auth'];

      if (($config['root']['login'] ?? false) == $login && ($config['root']['password'] ?? false) == $password) {
        Auth::getInstance()->set($config['root']);
        $this->redirect($this->getRouter()->assemble(['controller' => 'index']));
      }

      if (($config['source'] ?? false) === 'database') {

        $admin = ModelAbstract::one([
          'login' => $login,
          'password' => md5($password)
        ]);

        if ($admin) {

          $config['login'] = $admin->login;
          $config['password'] = $admin->password;

          Auth::getInstance()->set($config);
          $this->redirect($this->getRouter()->assemble(['controller' => 'index']));

          return [];
        }
      }

      $this->getResponse()->setStatusCode(400);
      return [];
    }

    $this->getView()->assign('title', Front::getInstance()->getConfig()['air']['admin']['title']);
    $this->getView()->setPath(__DIR__ . '/View');

    return $this->getView()->render('login');
  }

  /**
   * @return void
   * @throws DomainMustBeProvided
   * @throws RouterVarMustBeProvided
   */
  public function logout(): void
  {
    Auth::getInstance()->remove();
    $this->redirect($this->getRouter()->assemble(['action' => 'index']));
  }
}