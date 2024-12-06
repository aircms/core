<?php

declare(strict_types=1);

namespace Air\Crud\Controller\MultipleHelper;

use Air\Crud\Locale;
use Air\Crud\Model\History;
use Air\Model\ModelAbstract;
use Air\Model\Paginator;
use MongoDB\BSON\Regex;

trait Table
{
  protected function getConditions(array $filter = []): array
  {
    $conditions = [];

    foreach ($this->getFilterWithValues($filter) as $filter) {

      if (empty($filter['value'])) {
        continue;
      }

      if ($filter['type'] == 'search') {
        if (count($filter['by']) > 1) {
          foreach ($filter['by'] as $field) {
            if (str_starts_with($filter['value'], '!')) {
              $value = substr($filter['value'], 1);
              $conditions['$or'][] = [$field => ['$not' => new Regex(htmlspecialchars(quotemeta($value)), 'i')]];
            } else {
              $conditions['$or'][] = [$field => new Regex(htmlspecialchars(quotemeta($filter['value'])), 'i')];
            }
          }
        } else {
          if (str_starts_with($filter['value'], '!')) {
            $value = substr($filter['value'], 1);
            $conditions[$filter['by'][0]] = ['$not' => new Regex(htmlspecialchars(quotemeta($value)), 'i')];
          } else {
            $conditions[$filter['by'][0]] = new Regex(htmlspecialchars(quotemeta($filter['value'])), 'i');
          }
        }

      } else if ($filter['type'] == 'bool') {

        if ($filter['value'] == 'true') {
          $conditions[$filter['by'] ?? 'id'] = true;

        } else if ($filter['value'] == 'false') {
          $conditions[$filter['by'] ?? 'id'] = false;
        }

      } else if ($filter['type'] == 'model') {
        $conditions[$filter['by'] ?? 'id'] = $filter['value'];

      } else if ($filter['type'] == 'dateTime') {

        $from = strtotime($filter['value']['from']);
        $to = strtotime($filter['value']['to']);

        $dateTime = [];

        if ($from) {
          $dateTime['$gte'] = $from;
        }
        if ($to) {
          $dateTime['$lt'] = $to;
        }

        if (count($dateTime)) {
          $conditions[$filter['by']] = $dateTime;
        }

      } else {
        $conditions[$filter['by']] = $filter['value'];
      }
    }

    return $conditions;
  }

  protected function getFilterWithValues(array $filter = []): array
  {
    if (!count($filter)) {
      $filter = $this->getParam('filter', []);
    }
    $filters = [];
    foreach ($this->getFilter() as $_filter) {
      if ($_filter['type'] == 'search') {
        $_filter['value'] = $filter[$_filter['type']] ?? $_filter['value'] ?? null;
      } else {
        $_filter['value'] = $filter[$_filter['by']] ?? $_filter['value'] ?? null;
      }
      $filters[] = $_filter;
    }
    return $filters;
  }

  protected function getFilter(): array
  {
    return $this->getMods('filter');
  }

  protected function getSorting(): array
  {
    $sorting = $this->getMods('sorting');
    if (count($sorting)) {
      return $sorting[0];
    }
    $defaultSort = [];

    /** @var ModelAbstract $modelClassName */
    $modelClassName = $this->getModelClassName();

    $model = new $modelClassName();

    if ($model->getMeta()->hasProperty('position')) {
      $defaultSort = [
        'position' => 1
      ];
    }
    return array_merge($defaultSort, array_filter($this->getRequest()->getGet('sort', $defaultSort)));
  }

  protected function getHeader(): array
  {
    $headers = [];

    foreach ($this->getMods('header') as $header) {
      $headers[$header['by']] = $header;
    }

    if (!count($headers)) {

      $modelClassName = $this->getModelClassName();

      if (class_exists($modelClassName)) {

        /** @var ModelAbstract $model */
        /** @var ModelAbstract $modelClassName */
        $model = new $modelClassName;

        if ($model->getMeta()->hasProperty('image')) {
          $headers['image'] = ['title' => Locale::t('Image'), 'type' => 'image', 'static' => true];
        }
        if ($model->getMeta()->hasProperty('title')) {
          $headers['title'] = ['title' => Locale::t('Title'), 'static' => true];
        }
        if ($model->getMeta()->hasProperty('enabled')) {
          $headers['enabled'] = ['title' => Locale::t('Activity'), 'type' => 'bool'];
        }
      }
    }
    return $headers;
  }

  protected function getControls(): array
  {
    $controls = [];
    if ($this->getQuickManage()) {
      $controls[] = ['type' => 'view'];
    }
    if ($this->getPrintable()) {
      $controls[] = ['type' => 'print'];
    }
    if ($this->getManageable()) {
      $controls[] = ['type' => 'edit'];
    }
    $modelClassName = $this->getModelClassName();
    /** @var ModelAbstract $model */
    $model = new $modelClassName();
    if ($model->getMeta()->hasProperty('enabled')) {
      $controls[] = ['type' => 'enabled'];
    }
    $customControls = ($this->getMods('controls') ?? []);
    if (count($customControls)) {
      $controls = [...$controls, ...$customControls];
    }

    return $controls;
  }

  protected function getPaginator(): Paginator
  {
    /** @var ModelAbstract $modelClassName */
    $modelClassName = $this->getModelClassName();

    $paginator = new Paginator(
      new $modelClassName(),
      $this->getConditions(),
      $this->getSorting()
    );

    $page = $this->getParam('page', '1');

    if (!strlen($page)) {
      $page = 1;
    }

    $paginator->setPage(intval($page));

    $paginator->setItemsPerPage(
      $this->getItemsPerPage()
    );

    return $paginator;
  }

  protected function getItemsPerPage(): int
  {
    return $this->getMods('items-per-page') ?? 50;
  }

  public function index()
  {
    $this->adminLog(History::TYPE_READ_TABLE);

    $this->getView()->setVars([
      'icon' => $this->getIcon(),
      'title' => $this->getTitle(),
      'manageable' => $this->getManageable(),
      'manageableMultiple' => $this->getManageableMultiple(),
      'quickManage' => $this->getQuickManage(),
      'printable' => $this->getPrintable(),
      'positioning' => $this->getPositioning(),
      'exportable' => $this->getExportable(),

      'filter' => $this->getFilterWithValues(),
      'header' => $this->getHeader(),
      'headerButtons' => $this->getHeaderButtons(),
      'controls' => $this->getControls(),
      'paginator' => $this->getPaginator(),
      'controller' => $this->getRouter()->getController(),
    ]);

    $this->getView()->setScript('table/index');
  }
}