<?php

declare(strict_types=1);

$files = glob(__DIR__ . '/*.php');
if ($files === false) {
  throw new RuntimeException("Failed to glob for function files");
}
foreach ($files as $file) {
  require_once $file;
}
unset($file);
unset($files);