<?php

declare(strict_types=1);

namespace Air\Crud\Controller;

use Air\Crud\Locale;
use Throwable;
use Exception;

/**
 * @mod-sorting {"dateTime": -1}
 */
class History extends Multiple
{
  /**
   * @return string
   * @throws \Air\Core\Exception\ClassWasNotFound
   */
  protected function getTitle(): string
  {
    return Locale::t('Admin history');
  }

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
          ['value' => \Air\Crud\Model\History::TYPE_READ_TABLE, 'title' => Locale::t('Read table')],
          ['value' => \Air\Crud\Model\History::TYPE_READ_ENTITY, 'title' => Locale::t('Read entity')],
          ['value' => \Air\Crud\Model\History::TYPE_CREATE_ENTITY, 'title' => Locale::t('Create entity')],
          ['value' => \Air\Crud\Model\History::TYPE_WRITE_ENTITY, 'title' => Locale::t('Write entity')],
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
        'title' => Locale::t('User'),
        'source' => function (\Air\Crud\Model\History $adminHistory) {
          return $adminHistory->admin['login'];
        }],
      'dateTime' => ['title' => Locale::t('Date/Time'), 'type' => 'dateTime'],
      'type' => [
        'title' => Locale::t('Action'),
        'source' => function (\Air\Crud\Model\History $adminHistory) {

          $label = match ($adminHistory->type) {
            \Air\Crud\Model\History::TYPE_READ_TABLE => Locale::t('Table view'),
            \Air\Crud\Model\History::TYPE_READ_ENTITY => Locale::t('Record details'),
            \Air\Crud\Model\History::TYPE_WRITE_ENTITY => Locale::t('Edit record'),
            \Air\Crud\Model\History::TYPE_CREATE_ENTITY => Locale::t('Creating record'),
            default => Locale::t('Unknown'),
          };

          $style = match ($adminHistory->type) {
            \Air\Crud\Model\History::TYPE_READ_TABLE => self::INFO,
            \Air\Crud\Model\History::TYPE_READ_ENTITY => self::INFO,
            \Air\Crud\Model\History::TYPE_WRITE_ENTITY => self::WARNING,
            \Air\Crud\Model\History::TYPE_CREATE_ENTITY => self::WARNING,
            default => self::DANGER,
          };

          return self::badge($label, $style);
        }
      ],
      'section' => [
        'title' => Locale::t('Section'),
        'source' => function (\Air\Crud\Model\History $adminHistory) {
          $content = [self::label($adminHistory->section)];
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
          return self::multiple($content);
        }
      ],
    ];
  }
}
