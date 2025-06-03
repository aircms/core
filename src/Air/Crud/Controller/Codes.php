<?php

declare(strict_types=1);

namespace Air\Crud\Controller;

use Air\Core\Front;
use Air\Form\Form;
use Air\Form\Generator;
use Air\Form\Input;
use Air\Map;
use Air\Type\FaIcon;

/**
 * @mod-sortable title
 * @mod-quick-manage
 */
class Codes extends Multiple
{
  public static function render(): string
  {
    return implode("\n", Map::multiple(\Air\Crud\Model\Codes::all(), 'description'));
  }

  protected function getTitle(): string
  {
    return 'Codes';
  }

  public function getModelClassName(): string
  {
    return \Air\Crud\Model\Codes::class;
  }

  protected function getIcon(): string
  {
    return FaIcon::ICON_CODE;
  }

  protected function getEntity(): string
  {
    return Front::getInstance()->getConfig()['air']['admin']['codes'];
  }

  protected function getForm($model = null): Form
  {
    return Generator::full($model, [
      Input::textarea('description', description: 'Code will be used in HEAD section')
    ]);
  }
}
