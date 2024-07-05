<?php

declare(strict_types=1);

namespace Air\Crud\Controller;

use Air\Core\Exception\ClassWasNotFound;
use Air\Core\Front;
use Air\Crud\Locale;

/**
 * @mod-manageable true
 * @mod-sortable title
 */
class RobotsTxt extends Single
{
  /**
   * @return string
   * @throws ClassWasNotFound
   */
  protected function getTitle(): string
  {
    return Locale::t('Robots.txt');
  }

  /**
   * @return string
   */
  public function getModelClassName(): string
  {
    return \Air\Crud\Model\RobotsTxt::class;
  }

  /**
   * @return string[]
   */
  protected function getAdminMenuItem(): array
  {
    return ['icon' => 'robot'];
  }

  /**
   * @return string
   * @throws ClassWasNotFound
   */
  protected function getEntity(): string
  {
    return Front::getInstance()->getConfig()['air']['admin']['robotsTxt'];
  }
}
