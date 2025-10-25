<?php

declare(strict_types=1);

namespace Air\Crud\Controller;

use Air\Core\Front;
use Air\Crud\Locale;
use Air\Form\Form;
use Air\Form\Generator;
use Air\Form\Input;
use Air\Type\FaIcon;

class DeepSeek extends Single
{
  protected function getTitle(): string
  {
    return Locale::t('DeepSeek Settings');
  }

  public function getModelClassName(): string
  {
    return \Air\Crud\Model\DeepSeek::class;
  }

  protected function getIcon(): string
  {
    return FaIcon::ICON_ROBOT;
  }

  protected function getEntity(): string
  {
    return Front::getInstance()->getConfig()['air']['admin']['deepSeek'];
  }

  protected function getForm($model = null): ?Form
  {
    /** @var \Air\Crud\Model\DeepSeek $model */

    return Generator::full($model, [
      Input::text('key'),
      Input::text('model'),
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
}
