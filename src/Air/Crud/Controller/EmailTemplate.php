<?php

declare(strict_types=1);

namespace Air\Crud\Controller;

use Air\Core\Front;
use Air\Crud\Controller\MultipleHelper\Accessor\Header;
use Air\Crud\Locale;
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
    if (Front::getInstance()->getConfig()['air']['admin']['languages'] ?? false) {
      $headers[] = Header::language();
    }
    return $headers;
  }

  protected function getTitle(): string
  {
    return 'Email / Templates';
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
    return Front::getInstance()->getConfig()['air']['admin']['emailTemplates'];
  }

  protected function getForm($model = null): Form
  {
    return Generator::full($model, [
      Input::text('subject'),
      Input::tiny('body'),
    ]);
  }
}