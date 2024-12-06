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
 * @mod-quick-manage true
 */
class Phrase extends Multiple
{
  protected function getItemsPerPage(): int
  {
    return 100;
  }

  protected function getTitle(): string
  {
    return Locale::t('Phrases');
  }

  protected function getHeader(): array
  {
    return [
      'key' => ['title' => Locale::t('Key'), 'by' => 'key'],
      'value' => ['title' => Locale::t('Value'), 'source' => function (\Air\Crud\Model\Phrase $phrase) {
        return text(
          value: $phrase->value,
          class: 'form-control',
          attributes: ['style' => 'width: 600px;'],
          data: [
            'phrase-key' => $phrase->key,
            'phrase-language' => $phrase->language->id,
            'phrase-value' => $phrase->value
          ],
        );
      }],
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

  protected function getHeaderButtons(): array
  {
    return [
      ...parent::getHeaderButtons(),
      ...[[
        'title' => 'Save all',
        'url' => '" data-save-phrases-url="/' . $this->getEntity() . '/save" data-save-phrases="true',
      ]]
    ];
  }

  public function save(): void
  {
    $this->getView()->setLayoutEnabled(false);
    $this->getView()->setAutoRender(false);

    foreach ($this->getParam('phrases') ?? [] as $phrase) {
      $_phrase = \Air\Crud\Model\Phrase::fetchOne([
        'language' => $phrase['language'],
        'key' => $phrase['key']
      ]);

      $_phrase->value = $phrase['value'];
      $_phrase->isEdited = true;
      $_phrase->save();
    }
  }
}
