<?php

declare(strict_types=1);

namespace Air\Crud\Controller;

/**
 * @mod-title Logs
 *
 * @mod-sorting {"created": -1}
 *
 * @mod-filter {"type": "search", "by": ["title", "all"]}
 * @mod-filter {"type": "dateTime", "by": "created"}
 */
class Log extends Multiple
{
  /**
   * @return string
   */
  public function getModelClassName(): string
  {
    return \Air\Crud\Model\Log::class;
  }

  /**
   * @return string[]|null
   */
  protected function getAdminMenuItem(): array|null
  {
    return ['icon' => 'list'];
  }

  /**
   * @return array[]
   */
  protected function getHeader(): array
  {
    return [
      'title' => [
        'title' => 'Record',
        'source' => function (\Air\Crud\Model\Log $log) {
          return $this->getView()->render('log-item', ['log' => $log]);
        },
      ],
    ];
  }
}
