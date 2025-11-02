<?php

declare(strict_types=1);

namespace Air\Crud\Controller;

use Air\Core\Front;
use Air\Crud\Controller\MultipleHelper\Accessor\Header;
use Air\Crud\Locale;
use Air\Crud\Nav;
use Air\Form\Form;
use Air\Form\Generator;
use Air\Form\Input;
use Air\Type\FaIcon;

/**
 * @mod-controls {"type": "copy"}
 */
class SmsTemplate extends Multiple
{
  protected function getHeader(): array
  {
    $headers = [
      Header::text(by: 'url'),
      Header::longtext(by: 'message'),
      Header::enabled(),
    ];

    if (!!Nav::getSettingsItem(Nav::SETTINGS_LANGUAGES)) {
      $headers[] = Header::language();
    }
    return $headers;
  }

  protected function getTitle(): string
  {
    return 'SMS / Templates';
  }

  protected function getModelClassName(): string
  {
    return \Air\Crud\Model\SmsTemplate::class;
  }

  protected function getIcon(): string
  {
    return FaIcon::ICON_PAGE;
  }

  protected function getEntity(): string
  {
    return Nav::getSettingsItem(Nav::SETTINGS_SMS_TEMPLATES)['alias'];
  }

  protected function getForm($model = null): Form
  {
    return Generator::full($model, [
      Input::textarea('message'),
    ]);
  }
}