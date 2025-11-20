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
class EmailTemplate extends Multiple
{
  protected function getHeader(): array
  {
    $headers = [
      Header::text(by: 'url'),
      Header::text(by: 'subject'),
      Header::longtext(by: 'body'),
      Header::enabled(),
    ];
    if (Nav::getSettingsItem(Nav::SETTINGS_LANGUAGES)) {
      $headers[] = Header::language();
    }
    return $headers;
  }

  protected function getTitle(): string
  {
    return Locale::t('Email / Templates');
  }

  protected function getModelClassName(): string
  {
    return \Air\Crud\Model\EmailTemplate::class;
  }

  protected function getIcon(): string
  {
    return FaIcon::ICON_PAGE;
  }

  protected function getEntity(): string
  {
    return Nav::getSettingsItem(Nav::SETTINGS_EMAIL_TEMPLATES)['alias'];
  }

  /**
   * @param \Air\Crud\Model\EmailTemplate $model
   * @return Form
   */
  protected function getForm($model = null): Form
  {
    return Generator::full($model, [
      Input::text('url'),
      Input::text('subject'),
      Input::tiny('body'),
    ]);
  }
}