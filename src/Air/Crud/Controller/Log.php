<?php

declare(strict_types=1);

namespace Air\Crud\Controller;

use Air\Crud\Controller\MultipleHelper\Accessor\Control;
use Air\Crud\Controller\MultipleHelper\Accessor\Filter;
use Air\Crud\Controller\MultipleHelper\Accessor\Header;
use Air\Crud\Controller\MultipleHelper\Accessor\Ui;
use Air\Crud\Locale;
use Air\Type\FaIcon;

/**
 * @mod-sorting {"createdAt": -1}
 */
class Log extends Multiple
{
  public function getModelClassName(): string
  {
    return \Air\Crud\Model\Log::class;
  }

  protected function getIcon(): string
  {
    return FaIcon::ICON_LIST;
  }

  protected function getFilter(): array
  {
    return [
      Filter::search(by: ['title', 'search']),
      Filter::createdAt(),
      Filter::select(by: 'level', options: [
        \Air\Crud\Model\Log::INFO,
        \Air\Crud\Model\Log::ERROR,
      ])
    ];
  }

  protected function getHeader(): array
  {
    return [
      Header::source('Record', function (\Air\Crud\Model\Log $log) {
        return div(content: [
          div(class: 'mb-2 d-flex gap-2', content: [
            Ui::badge($log->level, match ($log->level) {
              \Air\Crud\Model\Log::INFO => Ui::PRIMARY,
              \Air\Crud\Model\Log::ERROR => Ui::DANGER,
              default => Ui::DARK
            }),
            Ui::badge(date('r', $log->createdAt), Ui::LIGHT),
          ]),
          div(class: 'mb-2', content: Ui::badge($log->title, Ui::DARK)),
          pre(content: json_encode($log->data, JSON_PRETTY_PRINT))
        ]);
      })
    ];
  }

  protected function getControls(): array
  {
    return [
      Control::html(['action' => 'details'], FaIcon::ICON_INFO_CIRCLE, Locale::t('Details'))
    ];
  }

  public function details(\Air\Crud\Model\Log $id): string
  {
    return Ui::card(content: [
      div(class: 'mb-2 d-flex gap-2', content: [
        Ui::badge($id->level, match ($id->level) {
          \Air\Crud\Model\Log::INFO => Ui::PRIMARY,
          \Air\Crud\Model\Log::ERROR => Ui::DANGER,
          default => Ui::DARK
        }),
        Ui::badge(date('r', $id->createdAt), Ui::LIGHT),
      ]),
      div(class: 'mb-2', content: Ui::badge($id->title, Ui::DARK)),
      pre(attributes: ['style' => 'max-height: 76vh; margin: 0;'], content: json_encode($id->data, JSON_PRETTY_PRINT))
    ]);
  }
}
