<?php

declare(strict_types=1);

namespace Air\Crud\Controller;

use Throwable;

class System extends Multiple
{
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
    } catch (Throwable) {
    }

    $this->getView()->setScript('system');
  }
}
