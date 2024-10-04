<?php

declare(strict_types=1);

namespace Air\Form\Element;

use Air\Core\Exception\ClassWasNotFound;
use Air\Core\Front;
use Air\Crud\Locale;

class Permissions extends ElementAbstract
{
  /**
   * @var string|null
   */
  public ?string $elementTemplate = 'form/element/permissions';

  /**
   * @param string $name
   * @param array $userOptions
   */
  public function __construct(string $name, array $userOptions = [])
  {
    $userOptions['allowNull'] = true;
    parent::__construct($name, $userOptions);
  }

  /**
   * @return array
   * @throws ClassWasNotFound
   */
  public function getPermissions(): array
  {
    $config = Front::getInstance()->getConfig()['air'];
    $permissions = Front::getInstance()->getConfig()['air']['admin']['menu'];
    $permissions[] = [
      'title' => Locale::t('System settings'),
      'items' => [
        [
          'title' => Locale::t('File storage'),
          'url' => ['controller' => $config['storage']['route']],
        ],
        [
          'title' => Locale::t('Fonts'),
          'url' => ['controller' => $config['admin']['fonts']],
        ],
        [
          'title' => Locale::t('System monitor'),
          'url' => ['controller' => $config['admin']['system']],
        ],
        [
          'title' => Locale::t('Languages'),
          'url' => ['controller' => $config['admin']['languages']],
        ],
        [
          'title' => Locale::t('Phrases'),
          'url' => ['controller' => $config['admin']['phrases']],
        ],
        [
          'title' => Locale::t('Logs'),
          'url' => ['controller' => $config['logs']['route']],
        ],
        [
          'title' => Locale::t('Administrators'),
          'url' => ['controller' => $config['admin']['manage']],
        ],
        [
          'title' => Locale::t('Administrator history'),
          'url' => ['controller' => $config['admin']['history']],
        ],
        [
          'title' => Locale::t('Codes'),
          'url' => ['controller' => $config['admin']['codes']],
        ],
        [
          'title' => Locale::t('Robots.txt'),
          'url' => ['controller' => $config['admin']['robotsTxt']],
        ],
      ]
    ];

    return $permissions;
  }

  /**
   * @return array
   * @throws ClassWasNotFound
   */
  public function getValue(): array
  {
    $selectedPermissions = [];
    $permissions = [];

    foreach ($this->getPermissions() as $groups) {
      foreach ($groups['items'] as $permission) {
        $permissions[md5(serialize($permission['url']))] = $permission['url'];
      }
    }

    $value = (array)parent::getValue();

    if (count($value)) {
      if (!isset($value[0])) {
        foreach (array_keys($value) as $permission) {
          $selectedPermissions[] = $permissions[$permission];
        }
      } else {
        foreach ($value as $permission) {
          $selectedPermissions[] = $permission;
        }
      }
    }

    return $selectedPermissions;
  }

  /**
   * @param array $route
   * @return bool
   * @throws ClassWasNotFound
   */
  public function isPermitted(array $route): bool
  {
    foreach ($this->getValue() as $permittedRoute) {
      if (md5(serialize($permittedRoute)) == md5(serialize($route['url']))) {
        return true;
      }
    }
    return false;
  }
}
