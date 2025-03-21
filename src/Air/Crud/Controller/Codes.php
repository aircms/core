<?php

declare(strict_types=1);

namespace Air\Crud\Controller;

use Air\Core\Front;
use Air\Crud\Locale;
use Air\Form\Element\Textarea;
use Air\Form\Form;
use Air\Form\Generator;
use Air\Map;

/**
 * @mod-manageable true
 * @mod-sortable title
 */
class Codes extends Multiple
{
  public static function render(): string
  {
    return implode('', Map::execute(\Air\Crud\Model\Codes::all(), 'description'));
  }

  protected function getTitle(): string
  {
    return Locale::t('Codes');
  }

  protected function getHeader(): array
  {
    return [
      'title' => ['title' => Locale::t('Title'), 'by' => 'title'],
      'enabled' => ['title' => Locale::t('Activity'), 'by' => 'enabled', 'type' => 'bool'],
    ];
  }

  public function getModelClassName(): string
  {
    return \Air\Crud\Model\Codes::class;
  }

  protected function getAdminMenuItem(): array
  {
    return ['icon' => 'code'];
  }

  protected function getEntity(): string
  {
    return Front::getInstance()->getConfig()['air']['admin']['codes'];
  }

  protected function getForm($model = null): Form
  {
    /** @var \Air\Crud\Model\Codes $model */

    return Generator::full($model, [
      Locale::t('General') => [
        new Textarea('description', [
          'value' => $model->description,
          'label' => Locale::t('Code'),
          'description' => Locale::t('Code will be used in HEAD section'),
          'allowNull' => false,
        ]),
      ],
    ]);
  }
}
