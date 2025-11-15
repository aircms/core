<?php

declare(strict_types=1);

namespace Air\Crud\Controller;

use Air\Crud\Locale;
use Air\Crud\Nav;
use Air\Form\Form;
use Air\Form\Generator;
use Air\Form\Input;
use Air\Type\FaIcon;

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

  protected function getIcon(): string
  {
    return FaIcon::ICON_COGS;
  }

  protected function getEntity(): string
  {
    return Nav::getSettingsItem(Nav::SETTINGS_EMAIL_SETTINGS)['alias'];
  }

  /**
   * @param \Air\Crud\Model\EmailSettings $model
   * @return Form
   */
  protected function getForm($model = null): Form
  {
    return Generator::full($model, [
      'General' => [
        Input::checkbox('emailQueueEnabled'),
        Input::select('gateway', allowNull: true, options: [
          \Air\Crud\Model\EmailSettings::GATEWAY_SMTP,
          \Air\Crud\Model\EmailSettings::GATEWAY_RESEND,
        ])
      ],
      'SMTP' => [
        Input::text('smtpServer', allowNull: true),
        Input::number('smtpPort', allowNull: true),
        Input::select('smtpProtocol', allowNull: true, options: [
          \Air\Crud\Model\EmailSettings::SSL,
          \Air\Crud\Model\EmailSettings::TSL
        ]),
        Input::text('smtpAddress', allowNull: true),
        Input::text('smtpPassword', allowNull: true),
        Input::text('smtpFromName', allowNull: true),
        Input::text('smtpFromAddress', allowNull: true),
      ],
      'Resend' => [
        Input::text('resendApiKey', allowNull: true),
        Input::text('resendFromEmail', allowNull: true),
        Input::text('resendFromName', allowNull: true),
      ]
    ]);
  }
}