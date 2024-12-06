<?php

declare(strict_types=1);

namespace Air\Crud\Controller\MultipleHelper;

use Air\Model\Driver\CursorAbstract;
use Air\Model\ModelAbstract;

trait Export
{
  protected function getExportFileName(): string
  {
    $controllerClassPars = explode('\\', get_class($this));
    return end($controllerClassPars);
  }

  protected function getExportTable(): array|CursorAbstract
  {
    /** @var ModelAbstract $modelClassName */
    $modelClassName = $this->getModelClassName();

    /** @var ModelAbstract $table */
    return $modelClassName::fetchAll(
      $this->getConditions(),
      $this->getSorting()
    );
  }

  public function export(): string
  {
    $this->getView()->setLayoutEnabled(false);

    ini_set('memory_limit', '-1');
    set_time_limit(0);

    $response = [];
    $table = $this->getExportTable();

    $header = array_keys($table[0]);

    $response[] = '"' . implode('","', $header) . '"';

    foreach ($table as $row) {
      $cols = [];
      foreach ($row as $value) {
        $cols[] = is_scalar($value) ? $value : $value($row);
      }
      $response[] = '"' . implode('","', $cols) . '"';
    }

    $response = implode(";\n", $response) . ';';

    $fileName = $this->getExportFileName() . '_' . date('c') . '.csv';
    $this->getResponse()->setHeader('Content-Disposition', 'attachment;filename=' . $fileName);
    $this->getResponse()->setHeader('Content-Size', (string)mb_strlen($response));

    return $response;
  }
}