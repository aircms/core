<?php

declare(strict_types=1);

namespace Air\Crud\Controller;

use Air\Core\Front;
use Air\Type\FaIcon;

class RobotsTxt extends Single
{
  protected function getTitle(): string
  {
    return 'Robots.txt';
  }

  public function getModelClassName(): string
  {
    return \Air\Crud\Model\RobotsTxt::class;
  }

  protected function getIcon(): string
  {
    return FaIcon::ICON_ROBOT;
  }

  protected function getEntity(): string
  {
    return Front::getInstance()->getConfig()['air']['admin']['robotsTxt'];
  }
}
