<?php

declare(strict_types=1);

namespace Air\Crud;

use Air\Cookie;
use Air\Core\Exception\ClassWasNotFound;
use Air\Core\Front;

class Auth
{
  /**
   * @var Auth|null
   */
  private static ?Auth $instance = null;

  /**
   * @var mixed
   */
  public mixed $identity = null;

  /**
   * @var string
   */
  private string $cookieName = 'authIdentity';

  /**
   * @throws ClassWasNotFound
   */
  private function __construct()
  {
    $this->cookieName = Front::getInstance()->getConfig()['air']['admin']['auth']['cookieName'] ?? $this->cookieName;
    $this->identity = Cookie::get($this->cookieName);
  }

  /**
   * @return Auth
   */
  public static function getInstance(): Auth
  {
    if (!self::$instance) {
      self::$instance = new self();
    }

    return self::$instance;
  }

  /**
   * @return bool
   */
  public function hasIdentity(): bool
  {
    return $this->identity !== null;
  }

  /**
   * @param $identity
   * @return bool
   */
  public function set($identity): bool
  {
    $this->identity = $identity;
    return Cookie::set($this->cookieName, $identity);
  }

  /**
   * @return mixed
   */
  public function get(): mixed
  {
    return $this->identity;
  }

  /**
   * @return bool
   */
  public function remove(): bool
  {
    $this->identity = null;
    return Cookie::remove($this->cookieName);
  }
}