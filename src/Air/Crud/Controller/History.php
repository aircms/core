<?php

declare(strict_types=1);

namespace Air\Crud\Controller;

use Throwable;
use Exception;

/**
 * @mod-title Admin history
 * @mod-sorting {"dateTime": -1}
 */
class History extends Multiple
{
  /**
   * @return array
   */
  public function getControls(): array
  {
    // TODO: Сделать просмотр подробностей
    return [];
  }

  /**
   * @return array
   * @throws Exception
   */
  protected function getHeaderButtons(): array
  {
    // TODO: Сделать удаление логов по фильтру
    return parent::getHeaderButtons();
  }

  /**
   * @return string
   */
  public function getModelClassName(): string
  {
    return \Air\Crud\Model\History::class;
  }

  /**
   * @return string[]
   */
  protected function getAdminMenuItem(): array
  {
    return ['icon' => 'clock'];
  }

  /**
   * @return array[]
   */
  public function getFilter(): array
  {
    return [
      ['type' => 'search', 'by' => ['search']],
      [
        'type' => 'select', 'by' => 'type',
        'options' => [
          ['value' => \Air\Crud\Model\History::TYPE_READ_TABLE, 'title' => 'Read table'],
          ['value' => \Air\Crud\Model\History::TYPE_READ_ENTITY, 'title' => 'Read entity'],
          ['value' => \Air\Crud\Model\History::TYPE_CREATE_ENTITY, 'title' => 'Create entity'],
          ['value' => \Air\Crud\Model\History::TYPE_WRITE_ENTITY, 'title' => 'Write entity'],
        ]
      ]
    ];
  }

  /**
   * @return array
   */
  public function getHeader(): array
  {
    return [
      'admin' => [
        'title' => 'User',
        'source' => function (\Air\Crud\Model\History $adminHistory) {
          return $adminHistory->admin['login'];
        }],
      'dateTime' => ['title' => 'Date/Time', 'type' => 'dateTime'],
      'type' => [
        'title' => 'Action',
        'source' => function (\Air\Crud\Model\History $adminHistory) {
          return match ($adminHistory->type) {
            \Air\Crud\Model\History::TYPE_READ_TABLE => "<span class='badge badge-info'>Table view</span>",
            \Air\Crud\Model\History::TYPE_READ_ENTITY => "<span class='badge badge-info'>Record details</span>",
            \Air\Crud\Model\History::TYPE_WRITE_ENTITY => "<span class='badge badge-warning'>Edit record</span>",
            \Air\Crud\Model\History::TYPE_CREATE_ENTITY => "<span class='badge badge-warning'>Creating record</span>",
            default => "<span class='badge badge-danger'>Unknown</span>",
          };
        }
      ],
      'section' => [
        'title' => 'Section',
        'source' => function (\Air\Crud\Model\History $adminHistory) {
          $content = "<b>{$adminHistory->section}</b>";
          try {
            $fields = [];
            foreach ($adminHistory->entity as $values) {
              if (is_string($values)) {
                $fields[] = $values;
              }
            }
            $content .= '<br>' . implode(', ', $fields);
          } catch (Throwable) {
          }
          return $content;
        }
      ],
    ];
  }
}
