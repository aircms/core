<?php

declare(strict_types=1);

namespace Air\Crud\Controller;

use Air\Core\Front;
use Air\Form\Element\Text;
use Air\Form\Form;
use Air\Form\Generator;
use Air\Form\Input;
use Air\Type\FaIcon;

class EmailSettings extends Single
{
  protected function getTitle(): string
  {
    return 'Email / Settings';
  }

  protected function getModelClassName(): string
  {
    return \Air\Crud\Model\EmailSettings::class;
  }

  protected function getIcon(): string
  {
    return FaIcon::ICON_COGS;
  }

  protected function getEntity(): string
  {
    return Front::getInstance()->getConfig()['air']['admin']['emailSettings'];
  }

  protected function getForm($model = null): Form
  {
    return Generator::full($model, [
      'SMTP Settings' => [
        Input::checkbox('emailQueueEnabled'),
        Input::text('server'),
        Input::text('port', type: Text::TYPE_NUMBER),
        Input::select('protocol', options: [
          \Air\Crud\Model\EmailSettings::SSL,
          \Air\Crud\Model\EmailSettings::TSL
        ]),
        Input::text('name'),
        Input::text('address'),
        Input::text('password'),
        Input::text('from'),
      ]
    ]);
  }
}