<?php

declare(strict_types=1);

namespace Air\Crud\Controller;

use Air\Core\Route;
use Air\Crud\Controller\MultipleHelper\Accessor\Control;
use Air\Crud\Controller\MultipleHelper\Accessor\Filter;
use Air\Crud\Controller\MultipleHelper\Accessor\Header;
use Air\Crud\Controller\MultipleHelper\Accessor\HeaderButton;
use Air\Crud\Controller\MultipleHelper\Accessor\Ui;

use Air\Crud\Locale;
use Air\Crud\Nav;
use Air\Type\FaIcon;

class Log extends Multiple
{
  protected function getSorting(): array
  {
    return [
      'createdAt' => -1
    ];
  }

  protected function getForm($model = null): null
  {
    return null;
  }

  protected function getHeaderButtons(): array
  {
    if ($nav = Nav::getSettingsItem(Nav::SETTINGS_LOGS)) {
      return [
        HeaderButton::item(Route::assembleRoute(controller: $nav['alias'], action: 'clear'), title: 'Clear')
      ];
    }
    return [];
  }

  protected function getTitle(): string
  {
    return Locale::t("Logs");
  }

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
      Header::source('Level', function (\Air\Crud\Model\Log $log) {
        return Ui::badge($log->level, match ($log->level) {
          \Air\Crud\Model\Log::INFO => Ui::PRIMARY,
          \Air\Crud\Model\Log::ERROR => Ui::DANGER,
          default => Ui::DARK
        });
      }),
      Header::source('Message', function (\Air\Crud\Model\Log $log) {
        return Ui::badge($log->title, Ui::DARK);
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

  public function clear(): void
  {
    \Air\Crud\Model\Log::batchRemove();

    if ($nav = Nav::getSettingsItem(Nav::SETTINGS_LOGS)) {
      $this->redirect(Route::assembleRoute(controller: $nav['alias']));
    }
  }

  public function details(\Air\Crud\Model\Log $id): string
  {
    return Ui::card(content: [
      div(class: 'd-flex mb-2 gap-2 justify-content-between', content: [
        div(class: 'd-flex gap-2', content: [
          Ui::badge($id->level, match ($id->level) {
            \Air\Crud\Model\Log::INFO => Ui::PRIMARY,
            \Air\Crud\Model\Log::ERROR => Ui::DANGER,
            default => Ui::DARK
          }),
          Ui::badge($id->title, Ui::DARK)
        ]),
        Ui::badge(date('r', $id->createdAt), Ui::LIGHT)
      ]),
      div(class: 'card bg-body p-3 mt-3', content: textarea(value: json_encode($id->data), data: ['json-viewer']))
    ]);
  }
}
