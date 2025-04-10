<?php

declare(strict_types=1);

namespace Air\Crud\Controller;

use Air\Core\Front;
use Air\Crud\Controller\MultipleHelper\Accessor\Header;
use Air\Form\Form;
use Air\Form\Generator;
use Air\Form\Input;
use Air\Type\FaIcon;

/**
 * @mod-manageable
 * @mod-sortable title
 */
class Language extends Multiple
{
  public static function isAvailable(): bool
  {
    return !!(Front::getInstance()->getConfig()['air']['admin']['languages'] ?? false);
  }

  protected function getTitle(): string
  {
    return 'Languages';
  }

  protected function getHeader(): array
  {
    return [
      Header::image(),
      Header::title(),
      Header::enabled(),
      Header::bool(by: 'isDefault')
    ];
  }

  public function getModelClassName(): string
  {
    return \Air\Crud\Model\Language::class;
  }

  protected function getIcon(): string
  {
    return FaIcon::ICON_GLOBE;
  }

  protected function getEntity(): string
  {
    return Front::getInstance()->getConfig()['air']['admin']['languages'];
  }

  protected function getForm($model = null): Form
  {
    return Generator::full($model, [
      Input::checkbox('isDefault'),
      Input::text('title'),
      Input::storage('image'),
      Input::text('key', description: '2 symbols, lowercase'),
    ]);
  }
}
