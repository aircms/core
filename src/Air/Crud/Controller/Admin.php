<?php

declare(strict_types=1);

namespace Air\Crud\Controller;

use Air\Core\Front;
use Air\Crud\Controller\MultipleHelper\Accessor\Header;
use Air\Crud\Locale;
use Air\Filter\Lowercase;
use Air\Filter\Trim;
use Air\Form\Element\Text;
use Air\Form\Form;
use Air\Form\Generator;
use Air\Form\Input;
use Air\Type\FaIcon;
use MongoDB\BSON\ObjectId;

/**
 * @mod-quick-manage
 */
class Admin extends Multiple
{
  protected function getHeader(): array
  {
    return [
      Header::text(by: 'name'),
      Header::text(by: 'login', size: Header::XL),
      Header::bool(by: 'isRoot'),
      Header::enabled(),
    ];
  }

  protected function getTitle(): string
  {
    return Locale::t('Administrators');
  }

  protected function getIcon(): string|null
  {
    return FaIcon::ICON_USERS;
  }

  public function getModelClassName(): string
  {
    return \Air\Crud\Model\Admin::class;
  }

  protected function getEntity(): string
  {
    return Front::getInstance()->getConfig()['air']['admin']['manage'];
  }

  protected function getForm($model = null): ?Form
  {
    return Generator::full($model, [
      'General' => [
        Input::checkbox('enabled'),
        Input::checkbox('isRoot'),
        Input::text(
          'login',
          filters: [Lowercase::class, Trim::class],
          validators: [function (string $login) use ($model): true|string {
            if ($model->id) {
              $exists = \Air\Crud\Model\Admin::count([
                'login' => $login,
                '_id' => ['$ne' => new ObjectId($model->id)]
              ]);
            } else {
              $exists = \Air\Crud\Model\Admin::count(['login' => $login]);
            }
            return $exists ? Locale::t('User with this login is already exists') : true;
          }]
        ),
      ],
      'Permissions' => [
        Input::permissions('permissions')
      ]
    ]);
  }
}
