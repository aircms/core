<?php

declare(strict_types=1);

namespace Air\Crud\Controller;

use Air\Core\Front;
use Air\Crud\Locale;
use Air\Form\Form;
use Air\Form\Generator;
use Air\Form\Input;
use Air\Type\FaIcon;

class OpenAi extends Single
{
  protected function getTitle(): string
  {
    return Locale::t('Open Ai Settings');
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
    return Front::getInstance()->getConfig()['air']['admin']['openAi'];
  }

  protected function getForm($model = null): ?Form
  {
    /** @var \Air\Crud\Model\OpenAi $model */

    return Generator::full($model, [
      Input::text('key'),
      Input::text('model'),
    ]);
  }
}
