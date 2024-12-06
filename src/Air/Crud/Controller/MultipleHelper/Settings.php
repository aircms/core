<?php

declare(strict_types=1);

namespace Air\Crud\Controller\MultipleHelper;

trait Settings
{
  protected function getManageable(): bool
  {
    return (bool)$this->getMods('manageable');
  }

  protected function getManageableMultiple(): bool
  {
    return (bool)$this->getMods('manageable-multiple');
  }

  protected function getQuickManage(): bool
  {
    return (bool)$this->getMods('quick-manage');
  }

  protected function getPrintable(): bool
  {
    return (bool)$this->getMods('printable');
  }

  protected function getHeaderButtons(): array
  {
    return $this->getMods('header-button');
  }

  protected function getPositioning(): string|false
  {
    return $this->getMods('sortable');
  }

  protected function getExportable(): bool
  {
    return (bool)$this->getMods('exportable');
  }
}