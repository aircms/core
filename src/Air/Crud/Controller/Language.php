<?php

declare(strict_types=1);

namespace Air\Crud\Controller;

use Air\Core\Exception\ClassWasNotFound;
use Air\Core\Front;
use Air\Form\Element\Checkbox;
use Air\Form\Form;
use Air\Form\Generator;
use Air\Form\Element\Text;
use Air\Form\Element\Textarea;
use Air\Form\Element\Storage;

/**
 * @mod-title Languages
 * @mod-manageable true
 *
 * @mod-header {"title": "Image", "by": "image", "type": "image"}
 * @mod-header {"title": "Title", "by": "title"}
 * @mod-header {"title": "Activity", "type": "bool", "by": "enabled"}
 * @mod-header {"title": "Default", "type": "bool", "by": "isDefault"}
 */
class Language extends Multiple
{
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
      'General' => [
        new Checkbox('isDefault', [
          'label' => 'Is default',
        ]),
        new Text('title', [
          'label' => 'Title',
          'allowNull' => false,
        ]),
        new Storage('image', [
          'label' => 'Flag',
          'allowNull' => false,
        ]),
        new Text('key', [
          'label' => 'Key',
          'description' => '2 symbols, lowercase'
        ])
      ]
    ]);
  }
}
