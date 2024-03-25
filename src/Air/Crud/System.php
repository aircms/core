<?php

declare(strict_types=1);

namespace Air\Crud;

use Air\Crud\Controller\Multiple;

/**
 * Class System
 * @package Air\Crud
 */
class System extends Multiple
{
  /**
   * @return void
   */
  public function index(): void
  {
    try {
      $this->getView()->setVars([
        'disk' => \Air\System::disk(),
        'memory' => \Air\System::memory(),
        'uptime' => \Air\System::uptime(true),
        'version' => \Air\System::version(),
        'cpuLoadAverage' => \Air\System::cpuLoadAverage(),
        'cpuCoreCount' => \Air\System::cpuCoreCount(),
        'cpuName' => \Air\System::cpuName(),
      ]);
    } catch (\Throwable) {
    }
    $this->getView()->setScript('system');
  }
}
