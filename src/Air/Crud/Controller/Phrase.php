<?php

declare(strict_types=1);

namespace Air\Crud\Controller;

use Air\Core\Exception\ClassWasNotFound;
use Air\Core\Front;
use Air\Form\Element\Text;
use Air\Form\Element\Textarea;
use Air\Form\Form;
use Air\Form\Generator;

/**
 * @mod-title Phrases
 * @mod-manageable true
 *
 * @mod-header {"title": "Key", "by": "key"}
 * @mod-header {"title": "Value", "by": "value"}
 * @mod-header {"title": "Language", "by": "language", "type": "model", "field": "title"}
 *
 * @mod-filter {"type": "search", "by": ["key", "value"]}
 * @mod-filter {"type": "model", "by": "language", "field": "title", "model": "\\Air\\Crud\\Model\\Language"}
 */
class Phrase extends Multiple
{
  /**
   * @return string
   */
  public function getModelClassName(): string
  {
    return \Air\Crud\Model\Phrase::class;
  }

  /**
   * @return string[]
   */
  protected function getAdminMenuItem(): array
  {
    return ['icon' => 'language'];
  }

  /**
   * @return string
   * @throws ClassWasNotFound
   */
  protected function getEntity(): string
  {
    return Front::getInstance()->getConfig()['air']['admin']['phrases'];
  }

  /**
   * @param \Air\Crud\Model\Codes $model
   * @return Form
   */
  protected function getForm($model = null): Form
  {
    return Generator::full($model, [
      'General' => [
        new Text('key', [
          'label' => 'Key',
        ]),
        new Text('value', [
          'label' => 'Value',
        ]),
      ],
    ]);
  }
}
