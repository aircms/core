<?php

declare(strict_types=1);

namespace Air\Form\Element;

use Air\Core\Front;
use Air\Crud\Locale;

class Permissions extends ElementAbstract
{
  public ?string $elementTemplate = 'form/element/permissions';

  public function __construct(string $name, array $userOptions = [])
  {
    $userOptions['allowNull'] = true;
    parent::__construct($name, $userOptions);
  }

  public function getPermissions(): array
  {
    $config = Front::getInstance()->getConfig()['air'];
    $permissions = Front::getInstance()->getConfig()['air']['admin']['menu'];

    $sections = [
      Locale::t('File storage') => $config['storage']['route'] ?? false,
      Locale::t('Fonts') => $config['admin']['fonts'] ?? false,
      Locale::t('System monitor') => $config['admin']['system'] ?? false,
      Locale::t('Languages') => $config['admin']['languages'] ?? false,
      Locale::t('Phrases') => $config['admin']['phrases'] ?? false,
      Locale::t('Logs') => $config['logs']['route'] ?? false,
      Locale::t('Administrators') => $config['admin']['manage'] ?? false,
      Locale::t('Administrator history') => $config['admin']['history'] ?? false,
      Locale::t('Codes') => $config['admin']['codes'] ?? false,
      Locale::t('Robots.txt') => $config['admin']['robotsTxt'] ?? false,
    ];

    foreach ($sections as $title => $controller) {
      $systemPermissions[] = [
        'title' => $title,
        'url' => ['controller' => $controller],
      ];
    }

    if (count($systemPermissions)) {
      $permissions[] = [
        'title' => Locale::t('System settings'),
        'items' => $systemPermissions
      ];
    }

    $_permissions = [];
    foreach ($permissions as $sectionIndex => $section) {
      foreach ($section['items'] as $page) {
        if ($page !== 'divider') {
          $_permissions[$sectionIndex] = $_permissions[$sectionIndex] ?? [
            'title' => $section['title'],
            'items' => []
          ];
          $_permissions[$sectionIndex]['items'][] = $page;
        }
      }
    }

    return $_permissions;
  }

  public function getValue(): array
  {
    $selectedPermissions = [];
    $permissions = [];

    foreach ($this->getPermissions() as $groups) {
      foreach ($groups['items'] as $permission) {
        try {
          $permissions[md5(serialize($permission['url']))] = $permission['url'];
        } catch (\Throwable) {
          var_dump($permission);
          die();
        }
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
