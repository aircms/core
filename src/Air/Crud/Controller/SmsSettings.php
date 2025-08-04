<?php

declare(strict_types=1);

namespace Air\Crud\Controller;

use Air\Core\Front;
use Air\Form\Form;
use Air\Form\Generator;
use Air\Form\Input;
use Air\Type\FaIcon;

class SmsSettings extends Single
{
  protected function getTitle(): string
  {
    return 'Sms / Settings';
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
    return Front::getInstance()->getConfig()['air']['admin']['smsSettings'];
  }

  protected function getForm($model = null): Form
  {
    $config = Front::getInstance()->getConfig()['air']['admin']['sms'] ?? [];

    return Generator::full($model, [
      'General' => [
        Input::checkbox('smsQueueEnabled'),
        Input::select('gateway', options: array_filter([
          ...in_array(\Air\Crud\Model\SmsSettings::GATEWAY_SMSTO, $config) ? [\Air\Crud\Model\SmsSettings::GATEWAY_SMSTO] : [],
          ...in_array(\Air\Crud\Model\SmsSettings::GATEWAY_GATEWAYAPI, $config) ? [\Air\Crud\Model\SmsSettings::GATEWAY_GATEWAYAPI] : [],
        ]))
      ],
      ...in_array(\Air\Crud\Model\SmsSettings::GATEWAY_SMSTO, $config) ? [
        'SMSto' => [
          Input::text('smstoApiKey', allowNull: true),
          Input::text('smstoSenderId', allowNull: true),
        ]
      ] : [],
      ...in_array(\Air\Crud\Model\SmsSettings::GATEWAY_GATEWAYAPI, $config) ? [
        'Gateway API' => [
          Input::text('gatewayapiApiKey', allowNull: true),
          Input::text('gatewayapiSender', allowNull: true),
        ]
      ] : [],
    ]);
  }
}