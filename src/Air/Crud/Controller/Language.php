<?php

declare(strict_types=1);

namespace Air\Crud\Controller;

use Air\Core\Exception\ClassWasNotFound;
use Air\Core\Front;
use Air\Crud\Locale;
use Air\Form\Element\Checkbox;
use Air\Form\Form;
use Air\Form\Generator;
use Air\Form\Element\Text;
use Air\Form\Element\Textarea;
use Air\Form\Element\Storage;

/**
 * @mod-manageable true
 * @mod-sortable title
 */
class Language extends Multiple
{
  /**
   * @return string
   * @throws ClassWasNotFound
   */
  protected function getTitle(): string
  {
    return Locale::t('Languages');
  }

  /**
   * @return array[]
   * @throws ClassWasNotFound
   */
  protected function getHeader(): array
  {
    return [
      'image' => ['title' => Locale::t('Image'), 'by' => 'image', 'type' => 'image'],
      'title' => ['title' => Locale::t('Title'), 'by' => 'title'],
      'enabled' => ['title' => Locale::t('Activity'), 'by' => 'enabled', 'type' => 'bool'],
      'isDefault' => ['title' => Locale::t('Default'), 'by' => 'isDefault', 'type' => 'bool'],
    ];
  }

  /**
   * @return string
   */
  public function getModelClassName(): string
  {
    return \Air\Crud\Model\Language::class;
  }

  /**
   * @return string[]
   */
  protected function getAdminMenuItem(): array
  {
    return ['icon' => 'globe'];
  }

  /**
   * @return string
   * @throws ClassWasNotFound
   */
  protected function getEntity(): string
  {
    return Front::getInstance()->getConfig()['air']['admin']['languages'];
  }

  /**
   * @param \Air\Crud\Model\Language $model
   * @return Form
   */
  protected function getForm($model = null): Form
  {
    return Generator::full($model, [
      Locale::t('General') => [
        new Checkbox('isDefault', [
          'label' => Locale::t('Is default'),
        ]),
        new Text('title', [
          'label' => Locale::t('Title'),
          'allowNull' => false,
        ]),
        new Storage('image', [
          'label' => Locale::t('Flag'),
          'allowNull' => false,
        ]),
        new Text('key', [
          'label' => Locale::t('Key'),
          'description' => Locale::t('2 symbols, lowercase')
        ])
      ]
    ]);
  }
}
