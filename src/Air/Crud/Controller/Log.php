<?php

declare(strict_types=1);

namespace Air\Crud\Controller;

use Air\Crud\Controller\MultipleHelper\Accessor\Filter;
use Air\Crud\Controller\MultipleHelper\Accessor\Header;
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
      Filter::search(by: ['title']),
      Filter::createdAt()
    ];
  }

  protected function getHeader(): array
  {
    return [
      Header::source('Record', function (\Air\Crud\Model\Log $log) {
        return $this->getView()->render('log-item', ['log' => $log]);
      })
    ];
  }
}
