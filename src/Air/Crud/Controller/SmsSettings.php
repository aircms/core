<?php

declare(strict_types=1);

namespace Air\Crud\Controller;

use Air\Crud\Locale;
use Air\Crud\Nav;
use Air\Form\Form;
use Air\Form\Generator;
use Air\Form\Input;
use Air\Type\FaIcon;

class SmsSettings extends Single
{
  protected function getTitle(): string
  {
    return Locale::t('Sms / Settings');
  }

  protected function getModelClassName(): string
  {
    return \Air\Crud\Model\SmsSettings::class;
  }

  protected function getIcon(): string
  {
    return FaIcon::ICON_COGS;
  }

  protected function getEntity(): string
  {
    return Nav::getSettingsItem(Nav::SETTINGS_SMS_SETTINGS)['alias'];
  }

  /**
   * @param \Air\Crud\Model\SmsSettings $model
   * @return Form
   */
  protected function getForm($model = null): Form
  {
    return Generator::full($model, [
      'General' => [
        Input::checkbox('smsQueueEnabled'),
        Input::select('gateway', options: array_filter([
          \Air\Crud\Model\SmsSettings::GATEWAY_SMSTO,
          \Air\Crud\Model\SmsSettings::GATEWAY_GATEWAYAPI,
        ]))
      ],
      'SMSto' => [
        Input::text('smstoApiKey', allowNull: true),
        Input::text('smstoSenderId', allowNull: true),
      ],
      'Gateway API' => [
        Input::text('gatewayapiApiKey', allowNull: true),
        Input::text('gatewayapiSender', allowNull: true),
      ],
    ]);
  }
}