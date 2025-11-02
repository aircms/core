<?php

declare(strict_types=1);

namespace Air\Crud\Controller;

use Air\Crud\Locale;
use Air\Crud\Nav;
use Air\Form\Form;
use Air\Form\Generator;
use Air\Form\Input;
use Air\Type\FaIcon;

class OpenAi extends Single
{
  protected function getTitle(): string
  {
    return Locale::t('Open Ai');
  }

  public function getModelClassName(): string
  {
    return \Air\Crud\Model\OpenAi::class;
  }

  protected function getIcon(): string
  {
    return FaIcon::ICON_ROBOT;
  }

  protected function getEntity(): string
  {
    return Nav::getSettingsItem(Nav::SETTINGS_OPENAI)['alias'];
  }

  /**
   * @param \Air\Crud\Model\OpenAi $model
   * @return Form|null
   */
  protected function getForm($model = null): ?Form
  {
    return Generator::full($model, [
      Input::text('key', allowNull: true),
      Input::text('model', allowNull: true),
    ]);
  }
}
