<?php

declare(strict_types=1);

namespace Air\Crud\Controller;

use Air\Core\Front;
use Air\Crud\Locale;
use Air\Crud\Nav;
use Air\Form\Form;
use Air\Form\Generator;
use Air\Form\Input;
use Air\Type\FaIcon;

class GoogleTranslate extends Single
{
  protected function getTitle(): string
  {
    return Locale::t('Google Translate Settings');
  }

  public function getModelClassName(): string
  {
    return \Air\Crud\Model\GoogleTranslate::class;
  }

  protected function getIcon(): string
  {
    return FaIcon::ICON_LANGUAGE;
  }

  protected function getEntity(): string
  {
    return Nav::getSettingsItem(Nav::SETTINGS_GOOGLE_TRANSLATE)['alias'];
  }

  /**
   * @param \Air\Crud\Model\GoogleTranslate $model
   * @return Form
   */
  protected function getForm($model = null): Form
  {
    return Generator::full($model, [
      Input::text('key', allowNull: true),
    ]);
  }

  public function phrase(): array
  {
    $language = \Air\Crud\Model\Language::fetchOne([
      'id' => $this->getParam('language')
    ]);

    return [
      'translation' =>
        \Air\ThirdParty\GoogleTranslate::instance()
          ->translate([$this->getParam('phrase')], $language)[0]
    ];
  }
}
