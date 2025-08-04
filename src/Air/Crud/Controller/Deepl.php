<?php

declare(strict_types=1);

namespace Air\Crud\Controller;

use Air\Core\Front;
use Air\Crud\Locale;
use Air\Form\Form;
use Air\Form\Generator;
use Air\Form\Input;
use Air\Type\FaIcon;

class Deepl extends Single
{
  protected function getTitle(): string
  {
    return Locale::t('Deepl Settings');
  }

  public function getModelClassName(): string
  {
    return \Air\Crud\Model\Deepl::class;
  }

  protected function getIcon(): string
  {
    return FaIcon::ICON_GLOBE;
  }

  protected function getEntity(): string
  {
    return Front::getInstance()->getConfig()['air']['admin']['deepl'];
  }

  protected function getForm($model = null): ?Form
  {
    /** @var \Air\Crud\Model\Deepl $model */

    return Generator::full($model, [
      Input::text('key'),
      Input::checkbox('isFree')
    ]);
  }

  public function phrase(): array
  {
    $language = \Air\Crud\Model\Language::fetchOne([
      'id' => $this->getParam('language')
    ]);

    return ['translation' => \Air\ThirdParty\Deepl::instance()->translate([$this->getParam('phrase')], $language)[0]];
  }
}
