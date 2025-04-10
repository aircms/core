<?php

declare(strict_types=1);

namespace Air\Crud\Controller;

use Air\Core\Front;
use Air\Crud\Controller\MultipleHelper\Accessor\Filter;
use Air\Crud\Controller\MultipleHelper\Accessor\Header;
use Air\Crud\Locale;
use Air\Form\Form;
use Air\Form\Generator;
use Air\Form\Input;
use Air\Model\ModelAbstract;
use Air\Type\FaIcon;

/**
 * @mod-quick-manage
 * @mod-items-per-page 100
 */
class Phrase extends Multiple
{
  protected function getTitle(): string
  {
    return Locale::t('Phrases');
  }

  protected function getHeader(): array
  {
    return [
      Header::text(by: 'key'),
      Header::source('Value', function (\Air\Crud\Model\Phrase $phrase) {
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
      }),
      Header::bool(by: 'isEdited'),
      Header::language()
    ];
  }

  protected function getFilter(): array
  {
    return [
      Filter::search(['key', 'value']),
      Filter::bool('Edited', 'isEdited', 'Edited', 'Not edited'),
      Filter::language()
    ];
  }

  public function getModelClassName(): string
  {
    return \Air\Crud\Model\Phrase::class;
  }

  protected function getIcon(): string
  {
    return FaIcon::ICON_LANGUAGE;
  }

  protected function getEntity(): string
  {
    return Front::getInstance()->getConfig()['air']['admin']['phrases'];
  }

  protected function getForm($model = null): Form
  {
    return Generator::full($model, [
      Input::text('key'),
      Input::text('value'),
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
