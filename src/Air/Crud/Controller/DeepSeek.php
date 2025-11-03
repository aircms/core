<?php

declare(strict_types=1);

namespace Air\Crud\Controller;

use Throwable;
use Air\Crud\Controller\MultipleHelper\Accessor\Ui;
use Air\Crud\Locale;
use Air\Crud\Nav;
use Air\Form\Form;
use Air\Form\Generator;
use Air\Form\Input;
use Air\Model\ModelAbstract;

class DeepSeek extends Single
{
  protected function getTitle(): string
  {
    return Nav::getSettingsItem(Nav::SETTINGS_DEEPSEEK)['title'];
  }

  public function getModelClassName(): string
  {
    return \Air\Crud\Model\DeepSeek::class;
  }

  protected function getIcon(): string
  {
    return Nav::getSettingsItem(Nav::SETTINGS_DEEPSEEK)['icon'];
  }

  protected function getEntity(): string
  {
    return Nav::getSettingsItem(Nav::SETTINGS_DEEPSEEK)['alias'];
  }

  /**
   * @param \Air\Crud\Model\DeepSeek $model
   * @return string|null
   */
  protected function getFormBlock(ModelAbstract $model): ?string
  {
    try {
      $deepSeek = new \Air\ThirdParty\DeepSeek();
      $balance = $deepSeek->balance();

      return div(content: [
        match ($balance['isAvailable']) {
          true => Ui::badge(Locale::t('Available'), Ui::SUCCESS),
          false => Ui::badge(Locale::t('Not available'), Ui::DANGER),
        },
        Ui::badge($balance['balance'], Ui::LIGHT),
      ]);
    } catch (Throwable) {
      return div(class: 'small text-muted', content: Locale::t('Balance is not available'));
    }
  }

  /**
   * @param \Air\Crud\Model\DeepSeek $model
   * @return Form|null
   */
  protected function getForm($model = null): ?Form
  {
    return Generator::full($model, [
      Input::text('key', allowNull: true),
      Input::text('model', allowNull: true),
    ]);
  }

  public function prompt(): void
  {
    $this->getView()->assign('preData', $this->getParam('preData'));
    $this->getView()->assign('name', $this->getParam('name'));
    $this->getView()->assign('value', $this->getParam('value'));
    $this->getView()->assign('language', $this->getParam('language'));

    $this->getView()->setScript('deepSeek/prompt');
  }

  public function ask(
    string                    $prompt,
    ?string                   $preData = null,
    ?string                   $value = null,
    ?string                   $name = null,
    ?\Air\Crud\Model\Language $language = null,
  ): array
  {
    $question = [];

    if ($preData) {
      $question[] = 'I have the following data object in JSON format: ';
      $question[] = $preData;
      $question[] = '----------------';
    }

    if ($name) {
      $question[] = 'I need to fill in the value for the field correctly:';
      $question[] = '"' . $name . '"';
      $question[] = '----------------';
    }

    if ($value) {
      $question[] = 'I have a guess, but I don\'t know if it\'s correct: ';
      $question[] = '"' . $value . '"';
      $question[] = '----------------';
    }

    $question[] = 'This is my goal question: ';
    $question[] = '"' . $prompt . '"';

    if ($language) {
      $question[] = '----------------';
      $question[] = 'I need to get an answer from you in the following language: ';
      $question[] = '"' . $language->title . '"';
    }

    $question[] = 'I need a strict JSON response, exactly like this: {"answer": "{{your-answer}}"}';

    return \Air\ThirdParty\DeepSeek::ask(implode("\n", $question), true);
  }

  public function phrase(string $phrase, \Air\Crud\Model\Language $language): array
  {
    $question = [
      'Translate this phrase into the following language: ' . $language->title,
      $phrase,
      '--------------',
      'I need a strict JSON response, exactly like this: {"translation": "{{translation}}"}'
    ];

    return \Air\ThirdParty\DeepSeek::ask(implode("\n", $question), true);
  }
}
