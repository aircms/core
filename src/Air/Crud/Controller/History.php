<?php

declare(strict_types=1);

namespace Air\Crud\Controller;

use Air\Crud\Controller\MultipleHelper\Accessor\Filter;
use Air\Crud\Controller\MultipleHelper\Accessor\Header;
use Air\Crud\Controller\MultipleHelper\Accessor\Ui;
use Air\Crud\Locale;
use Air\Type\FaIcon;
use Throwable;

/**
 * @mod-sorting {"createdAt": -1}
 */
class History extends Multiple
{
  protected function getTitle(): string
  {
    return 'Admin history';
  }

  protected function getHeaderButtons(): array
  {
    // TODO: Сделать удаление логов по фильтру
    return parent::getHeaderButtons();
  }

  public function getModelClassName(): string
  {
    return \Air\Crud\Model\History::class;
  }

  protected function getIcon(): string
  {
    return FaIcon::ICON_CLOCK;
  }

  public function getFilter(): array
  {
    return [
      Filter::search(),
      Filter::select('Action', 'type', options: [
        \Air\Crud\Model\History::TYPE_READ_TABLE,
        \Air\Crud\Model\History::TYPE_READ_ENTITY,
        \Air\Crud\Model\History::TYPE_CREATE_ENTITY,
        \Air\Crud\Model\History::TYPE_WRITE_ENTITY,
      ])
    ];
  }

  public function getHeader(): array
  {
    return [
      Header::source(Locale::t('User'), fn(\Air\Crud\Model\History $adminHistory) => $adminHistory->admin['login'] ?? $adminHistory->admin[0]),
      Header::createdAt(),
      Header::source(Locale::t('Action'), fn(\Air\Crud\Model\History $adminHistory) => Ui::badge(
        match ($adminHistory->type) {
          \Air\Crud\Model\History::TYPE_READ_TABLE => Locale::t('Table view'),
          \Air\Crud\Model\History::TYPE_READ_ENTITY => Locale::t('Record details'),
          \Air\Crud\Model\History::TYPE_WRITE_ENTITY => Locale::t('Edit record'),
          \Air\Crud\Model\History::TYPE_CREATE_ENTITY => Locale::t('Creating record'),
          default => Locale::t('Unknown'),
        },
        match ($adminHistory->type) {
          \Air\Crud\Model\History::TYPE_READ_TABLE, \Air\Crud\Model\History::TYPE_READ_ENTITY => Ui::INFO,
          \Air\Crud\Model\History::TYPE_WRITE_ENTITY, \Air\Crud\Model\History::TYPE_CREATE_ENTITY => Ui::WARNING,
          default => Ui::DANGER,
        }
      )),
      Header::source(Locale::t('Section'), function (\Air\Crud\Model\History $adminHistory) {
        $content = [Ui::label($adminHistory->section)];
        try {
          $fields = [];
          foreach ($adminHistory->entity as $values) {
            if (is_string($values)) {
              $fields[] = $values;
            }
          }
          $content[] .= implode(', ', $fields);
        } catch (Throwable) {
        }
        return Ui::multiple($content);
      })
    ];
  }
}
