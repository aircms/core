<?php

declare(strict_types=1);

namespace Air\Crud\Controller;

use Air\Core\Exception\ClassWasNotFound;
use Air\Core\Front;
use Air\Form\Element\Textarea;
use Air\Form\Form;
use Air\Form\Generator;

/**
 * @mod-title Codes
 * @mod-manageable true
 * @mod-sortable title
 *
 * @mod-header {"title": "Title", "by": "title"}
 * @mod-header {"title": "Activity", "type": "bool", "by": "enabled"}
 */
class Codes extends Multiple
{
  /**
   * @return string
   */
  public function getModelClassName(): string
  {
    return \Air\Crud\Model\Codes::class;
  }

  /**
   * @return string[]
   */
  protected function getAdminMenuItem(): array
  {
    return ['icon' => 'code'];
  }

  /**
   * @return string
   * @throws ClassWasNotFound
   */
  protected function getEntity(): string
  {
    return Front::getInstance()->getConfig()['air']['admin']['fonts'];
  }

  /**
   * @param \Air\Crud\Model\Codes $model
   * @return Form
   */
  protected function getForm($model = null): Form
  {
    return Generator::full($model, [
      'General' => [
        new Textarea('description', [
          'value' => $model->description,
          'label' => 'Code',
          'description' => 'Code will be used in HEAD section',
          'allowNull' => false,
        ]),
      ],
    ]);
  }
}
