<?php

declare(strict_types=1);

namespace Air\Crud\Controller;

use Air\Crud\Controller\MultipleHelper\Accessor\Control;
use Air\Crud\Controller\MultipleHelper\Accessor\Filter;
use Air\Crud\Controller\MultipleHelper\Accessor\Header;
use Air\Crud\Controller\MultipleHelper\Accessor\Ui;
use Air\Crud\Locale;
use Air\Crud\Nav;
use Air\Type\FaIcon;

class OpenAiLog extends Multiple
{
  protected function getTitle(): string
  {
    return Nav::getSettingsItem(Nav::SETTINGS_OPENAI_LOG)['title'];
  }

  public function getModelClassName(): string
  {
    return \Air\Crud\Model\OpenAiLog::class;
  }

  protected function getIcon(): string
  {
    return Nav::getSettingsItem(Nav::SETTINGS_OPENAI_LOG)['icon'];
  }

  protected function getEntity(): string
  {
    return Nav::getSettingsItem(Nav::SETTINGS_OPENAI_LOG)['alias'];
  }

  protected function getFilter(): array
  {
    return [
      Filter::search(by: ['model', 'key']),
      Filter::createdAt()
    ];
  }

  protected function getBlock(): ?string
  {
    $totalTokens = 0;
    $inputTokens = 0;
    $outputTokens = 0;

    foreach (\Air\Crud\Model\OpenAiLog::fetchAll($this->getConditions()) as $openAiLog) {
      $totalTokens += $openAiLog->totalTokens;
      $inputTokens += $openAiLog->inputTokens;
      $outputTokens += $openAiLog->outputTokens;
    }

    return div(content: Ui::multipleLine([
      Ui::badge($inputTokens, Ui::LIGHT),
      ' + ',
      Ui::badge($outputTokens, Ui::DARK),
      ' = ',
      Ui::badge($totalTokens),
    ]));
  }

  protected function getHeader(): array
  {
    return [
      Header::source('Model', function (\Air\Crud\Model\OpenAiLog $openAiLog) {
        return Ui::multiple([
          Ui::badge($openAiLog->model),
          Ui::badge($openAiLog->key, Ui::DARK)
        ]);
      }),
      Header::source('Tokens', function (\Air\Crud\Model\OpenAiLog $openAiLog) {
        return Ui::multipleLine([
          Ui::badge($openAiLog->inputTokens, Ui::LIGHT),
          ' + ',
          Ui::badge($openAiLog->outputTokens, Ui::DARK),
          ' = ',
          Ui::badge($openAiLog->totalTokens),
        ]);
      }),
      Header::createdAt(),
    ];
  }

  protected function getControls(): array
  {
    return [
      Control::html(['action' => 'details'], FaIcon::ICON_INFO_CIRCLE, Locale::t('Details'))
    ];
  }

  public function details(\Air\Crud\Model\OpenAiLog $id): string
  {
    return Ui::card(content: [
      div(class: 'd-flex mb-2 gap-2 align-items-start justify-content-between', content: [
        div(class: 'd-flex gap-2 align-items-start', content: [
          Ui::badge($id->model),
          Ui::badge($id->key, Ui::DARK),
          Ui::multipleLine([
            Ui::badge($id->inputTokens, Ui::LIGHT),
            ' + ',
            Ui::badge($id->outputTokens, Ui::DARK),
            ' = ',
            Ui::badge($id->totalTokens),
          ])
        ]),
        Ui::badge(date('r', $id->createdAt), Ui::LIGHT)
      ]),
      div(class: 'mt-3', content: div(class: 'row', content: [
        div(class: 'col-6', content: div(class: 'card bg-body p-3', content: textarea(value: json_encode($id->input), data: ['json-viewer']))),
        div(class: 'col-6', content: div(class: 'card bg-body p-3', content: textarea(value: json_encode($id->output), data: ['json-viewer']))),
      ]))
    ]);
  }

  /**
   * @param \Air\Crud\Model\OpenAiLog $model
   * @return null
   */
  protected function getForm($model = null): null
  {
    return null;
  }
}
