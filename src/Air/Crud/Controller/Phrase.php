<?php

declare(strict_types=1);

namespace Air\Crud\Controller;

use Air\Core\Exception\ClassWasNotFound;
use Air\Core\Front;
use Air\Crud\Locale;
use Air\Form\Element\Text;
use Air\Form\Element\Textarea;
use Air\Form\Form;
use Air\Form\Generator;

/**
 * @mod-manageable true
 */
class Phrase extends Multiple
{
  /**
   * @return string
   * @throws ClassWasNotFound
   */
  protected function getTitle(): string
  {
    return Locale::t('Phrases');
  }

  /**
   * @return array
   */
  protected function getHeader(): array
  {
    return [
      'key' => ['title' => Locale::t('Key'), 'by' => 'key'],
      'value' => ['title' => Locale::t('Value'), 'by' => 'value'],
      'language' => ['title' => Locale::t('Language'), 'by' => 'language', 'type' => 'model', 'field' => 'title'],
    ];
  }

  /**
   * @return array
   */
  protected function getFilter(): array
  {
    return [
      ['type' => 'search', 'by' => ['key', 'value']],
      ['type' => 'model', 'by' => 'language', 'field' => 'title', 'model' => \Air\Crud\Model\Language::class]
    ];
  }

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
      Locale::t('General') => [
        new Text('key', [
          'label' => Locale::t('Key'),
        ]),
        new Text('value', [
          'label' => Locale::t('Value'),
        ]),
      ],
    ]);
  }
}
