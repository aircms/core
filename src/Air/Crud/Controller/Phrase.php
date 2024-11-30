<?php

declare(strict_types=1);

namespace Air\Crud\Controller;

use Air\Core\Front;
use Air\Crud\Locale;
use Air\Form\Element\Text;
use Air\Form\Form;
use Air\Form\Generator;
use Air\Model\ModelAbstract;

/**
 * @mod-manageable true
 */
class Phrase extends Multiple
{
  protected function getItemsPerPage(): int
  {
    return 20;
  }

  protected function getTitle(): string
  {
    return Locale::t('Phrases');
  }

  protected function getHeader(): array
  {
    return [
      'key' => ['title' => Locale::t('Key'), 'by' => 'key'],
      'value' => ['title' => Locale::t('Value'), 'by' => 'value'],
      'isEdited' => ['title' => Locale::t('Edited'), 'by' => 'isEdited', 'type' => 'bool'],
      'language' => ['title' => Locale::t('Language'), 'by' => 'language', 'type' => 'model', 'field' => 'title'],
    ];
  }

  protected function getFilter(): array
  {
    return [
      ['type' => 'search', 'by' => ['key', 'value']],
      ['type' => 'bool', 'by' => 'isEdited', 'true' => 'Edited', 'false' => 'Not edited'],
      ['type' => 'model', 'by' => 'language', 'field' => 'title', 'model' => \Air\Crud\Model\Language::class]
    ];
  }

  public function getModelClassName(): string
  {
    return \Air\Crud\Model\Phrase::class;
  }

  protected function getAdminMenuItem(): array
  {
    return ['icon' => 'language'];
  }

  protected function getEntity(): string
  {
    return Front::getInstance()->getConfig()['air']['admin']['phrases'];
  }

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

  protected function didSaved(ModelAbstract $model, array $formData, ModelAbstract $oldModel): void
  {
    /** @var \Air\Crud\Model\Phrase $model */

    $model->isEdited = true;
    $model->save();
  }
}
