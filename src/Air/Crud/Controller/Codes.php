<?php

declare(strict_types=1);

namespace Air\Crud\Controller;

use Air\Core\Exception\ClassWasNotFound;
use Air\Core\Front;
use Air\Crud\Locale;
use Air\Form\Element\Textarea;
use Air\Form\Form;
use Air\Form\Generator;

/**
 * @mod-manageable true
 * @mod-sortable title
 */
class Codes extends Multiple
{
  /**
   * @return string
   * @throws ClassWasNotFound
   */
  protected function getTitle(): string
  {
    return Locale::t('Codes');
  }

  /**
   * @return array[]
   * @throws ClassWasNotFound
   */
  protected function getHeader(): array
  {
    return [
      'title' => ['title' => Locale::t('Title'), 'by' => 'title'],
      'enabled' => ['title' => Locale::t('Activity'), 'by' => 'enabled', 'type' => 'bool'],
    ];
  }

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
    return Front::getInstance()->getConfig()['air']['admin']['codes'];
  }

  /**
   * @param \Air\Crud\Model\Codes $model
   * @return Form
   */
  protected function getForm($model = null): Form
  {
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
