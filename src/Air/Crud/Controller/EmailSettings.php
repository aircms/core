<?php

declare(strict_types=1);

namespace Air\Crud\Controller;

use Air\Core\Front;
use Air\Crud\Locale;
use Air\Form\Element\Checkbox;
use Air\Form\Element\Select;
use Air\Form\Element\Text;
use Air\Form\Form;
use Air\Form\Generator;

class EmailSettings extends Single
{
  protected function getTitle(): string
  {
    return Locale::t('Email / Settings');
  }

  protected function getModelClassName(): string
  {
    return \Air\Crud\Model\EmailSettings::class;
  }

  protected function getAdminMenuItem(): array|null
  {
    return ['icon' => 'cogs'];
  }

  protected function getEntity(): string
  {
    return Front::getInstance()->getConfig()['air']['admin']['emailSettings'];
  }

  protected function getForm($model = null): Form
  {
    /** @var \Air\Crud\Model\EmailSettings $model */

    return Generator::full($model, [
      'SMTP Settings' => [
        new Checkbox('emailQueueEnabled'),
        new Text('server'),
        new Text('port'),
        new Select('protocol', [
          'options' => [
            ['title' => 'SSL', 'value' => \Air\Crud\Model\EmailSettings::SSL],
            ['title' => 'TSL', 'value' => \Air\Crud\Model\EmailSettings::TSL],
          ],
        ]),
        new Text('name'),
        new Text('address'),
        new Text('password'),
        new Text('from'),
      ]
    ]);
  }
}