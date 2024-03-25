<?php

declare(strict_types=1);

namespace Air\Crud\Controller;

use Exception;
use Air\Core\Exception\ClassWasNotFound;
use Air\Core\Exception\DomainMustBeProvided;
use Air\Core\Exception\RouterVarMustBeProvided;
use Air\Core\Front;
use Air\Crud\AdminHistory\Controller;
use Air\Crud\Auth;
use Air\Crud\AuthCrud;
use Air\Form\Form;
use Air\Form\Generator;
use Air\Map;
use Air\Model\Exception\CallUndefinedMethod;
use Air\Model\Exception\ConfigWasNotProvided;
use Air\Model\Exception\DriverClassDoesNotExists;
use Air\Model\Exception\DriverClassDoesNotExtendsFromDriverAbstract;
use Air\Model\ModelAbstract;
use Air\Model\ModelInterface;
use Air\Model\Paginator;
use MongoDB\BSON\Regex;
use ReflectionClass;

abstract class Multiple extends AuthCrud
{
  /**
   * @var ModelAbstract|null
   */
  public ?ModelAbstract $model = null;

  /**
   * @return string
   * @throws ClassWasNotFound
   */
  protected function getModelClassName(): string
  {
    $controllerClassPars = explode('\\', get_class($this));

    $entity = end($controllerClassPars);

    return implode('\\', [
      Front::getInstance()->getConfig()['air']['loader']['namespace'],
      'Model',
      $entity
    ]);
  }

  /**
   * @return string
   */
  protected function getEntity(): string
  {
    $controllerClassPars = explode('\\', get_class($this));
    return end($controllerClassPars);
  }

  /**
   * @return array
   * @throws Exception
   */
  protected function getConditions(): array
  {
    $conditions = [];

    foreach ($this->getFilterWithValues() as $filter) {

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

  /**
   * @return array
   * @throws Exception
   */
  protected function getFilterWithValues(): array
  {
    $filters = [];

    foreach ($this->getFilter() as $filter) {

      if ($filter['type'] == 'search') {
        $filter['value'] = $this->getParam('filter', [])[$filter['type']] ?? $filter['value'] ?? null;

      } else {
        $filter['value'] = $this->getParam('filter', [])[$filter['by']] ?? $filter['value'] ?? null;
      }

      $filters[] = $filter;
    }

    return $filters;
  }

  /**
   * @return array
   * @throws Exception
   */
  protected function getFilter(): array
  {
    return $this->getMods('filter');
  }

  /**
   * @return array
   * @throws Exception
   */
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

  /**
   * @return string|true
   * @throws Exception
   */
  protected function getPositioning(): string|false
  {
    return $this->getMods('sortable');
  }

  /**
   * @return string
   * @throws Exception
   */
  protected function getTitle(): string
  {
    return $this->getMods('title');
  }

  /**
   * @return bool
   * @throws Exception
   */
  protected function getManageable(): bool
  {
    return (bool)$this->getMods('manageable');
  }

  /**
   * @return array
   * @throws Exception
   */
  protected function getHeaderButtons(): array
  {
    return $this->getMods('header-button');
  }

  /**
   * @return array
   * @throws Exception
   */
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
          $headers['image'] = ['title' => 'Image', 'type' => 'image', 'static' => true];
        }
        if ($model->getMeta()->hasProperty('title')) {
          $headers['title'] = ['title' => 'Title', 'static' => true];
        }
        if ($model->getMeta()->hasProperty('enabled')) {
          $headers['enabled'] = ['title' => 'Activity', 'type' => 'bool'];
        }
      }
    }
    return $headers;
  }

  /**
   * @return array
   * @throws Exception
   */
  protected function getControls(): array
  {
    $controls = [];

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

  /**
   * @return Paginator
   * @throws Exception
   */
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

  /**
   * @return int
   * @throws Exception
   */
  protected function getItemsPerPage(): int
  {
    return $this->getMods('items-per-page') ?? 50;
  }

  /**
   * @return array
   * @throws Exception
   */
  protected function getExportHeader(): array
  {
    return $this->getMods('export') ?? [];
  }

  /**
   * @return string
   */
  protected function getExportFileName(): string
  {
    $controllerClassPars = explode('\\', get_class($this));
    return end($controllerClassPars);
  }

  /**
   * @param $model
   * @return Form
   */
  protected function getForm($model = null): Form
  {
    $formClassName = $this->getFormClassName();

    if (class_exists($formClassName)) {
      /** @var Form $formClassName */
      return new $formClassName(['data' => $model]);
    }

    return Generator::full($model);
  }

  /**
   * @param string $id
   * @param bool $enabled
   * @return void
   * @throws CallUndefinedMethod
   * @throws ClassWasNotFound
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   */
  public function setEnabled(string $id, bool $enabled): void
  {
    /** @var ModelAbstract $modelClassName */
    $modelClassName = $this->getModelClassName();

    $record = $modelClassName::fetchOne(['id' => $id]);

    $record->enabled = $enabled;
    $record->save();

    if ($record->enabled) {
      $this->didEnabled($record);
    } else {
      $this->didDisable($record);
    }
  }

  /**
   * @return void
   * @throws DomainMustBeProvided
   * @throws RouterVarMustBeProvided
   * @throws ClassWasNotFound
   */
  public function init(): void
  {
    parent::init();

    $this->getView()->setLayoutEnabled(
      !$this->getRequest()->isAjax()
    );

    $this->getView()->setLayoutTemplate('index');
    $this->getView()->setAutoRender(true);

    $this->getView()->setPath(realpath(__DIR__ . '/../View'));
  }

  /**
   * @param string $type
   * @param array $entity
   * @param string|null $section
   * @param array $was
   * @param array $became
   * @return void
   * @throws CallUndefinedMethod
   * @throws ClassWasNotFound
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   */
  protected function adminLog(
    string $type,
    array  $entity = [],
    string $section = null,
    array  $was = [],
    array  $became = []
  ): void
  {
    if (Front::getInstance()->getConfig()['air']['admin']['history'] ?? false) {
      if ($this instanceof Controller) {
        return;
      }
      $section = $section ?? $this->title ?? null;
      if (!$section) {
        $section = strtolower($this->getRouter()->getController());
        foreach (Front::getInstance()->getConfig()['air']['admin']['menu'] ?? [] as $menu) {
          foreach ($menu['items'] as $subMenu) {
            if (isset($subMenu['url'])) {
              if (strtolower($subMenu['url']['controller']) == $section) {
                $section = $subMenu['title'];
              }
            }
          }
        }
      }

      $history = new \Air\Crud\AdminHistory\ModelAbstract();
      $data = [
        'dateTime' => time(),
        'admin' => Auth::getInstance()->get(),
        'type' => $type,
        'section' => $section,
        'entity' => $entity,
        'was' => $was,
        'became' => $became
      ];
      $data['search'] = serialize($data);
      $history->populateWithoutQuerying($data);
      $history->save();
    }
  }

  /**
   * @return array|null
   * @throws ClassWasNotFound
   */
  protected function getAdminMenuItem(): array|null
  {
    $controllerClassPars = explode('\\', get_class($this));
    $section = strtolower(end($controllerClassPars));

    foreach (Front::getInstance()->getConfig()['air']['admin']['menu'] ?? [] as $menu) {
      foreach ($menu['items'] as $subMenu) {
        if (strtolower($subMenu['url']['controller'] ?? '') == $section) {
          return $subMenu;
        }
      }
    }
    return null;
  }

  /**
   * @param string $type
   * @return array|false|mixed
   * @throws Exception
   */
  protected function getMods(string $type): mixed
  {
    $reflection = new ReflectionClass(static::class);

    $mods = array_values(array_map(function ($item) use ($type) {
      return trim(str_replace('@mod-' . $type . " ", '', $item));
    }, array_filter(
      explode("\n", str_replace('*', ' ', $reflection->getDocComment())),
      function ($item) use ($type) {
        return strstr($item, '@mod-' . $type . " ");
      }
    )));

    if ($type == 'title') {
      return $mods[0] ?? '';
    }

    if ($type == 'items-per-page') {
      if (isset($mods[0])) {
        return (int)$mods[0];
      }
      return null;
    }

    if ($type == 'sortable') {
      return $mods[0] ?? false;
    }

    foreach ($mods as $index => $mod) {
      if ($mod = json_decode($mod, true)) {
        $mods[$index] = $mod;
        continue;
      }
      throw new Exception("Modification with type: {$type} have not a valid JSON: {$mod}");
    }

    return $mods;
  }

  /**
   * @return string
   */
  protected function getFormClassName(): string
  {
    $controllerClassPars = explode('\\', get_class($this));
    $controllerClassPars[count($controllerClassPars) - 2] = 'Form';
    return implode('\\', $controllerClassPars);
  }

  /**
   * @param ModelAbstract $model
   * @param array $formData
   * @return void
   */
  protected function didCreated(ModelAbstract $model, array $formData)
  {
  }

  /**
   * @param ModelAbstract $model
   * @param array $formData
   * @return void
   */
  protected function didChanged(ModelAbstract $model, array $formData): void
  {
  }

  /**
   * @param ModelAbstract $model
   * @param array $formData
   */
  protected function didSaved(ModelAbstract $model, array $formData)
  {
  }

  /**
   * @param ModelAbstract $model
   * @return void
   */
  protected function didEnabled(ModelAbstract $model)
  {
  }

  /**
   * @param ModelAbstract $model
   * @return void
   */
  protected function didDisable(ModelAbstract $model)
  {
  }

  /**
   * @return string|void
   * @throws ClassWasNotFound
   * @throws DomainMustBeProvided
   * @throws RouterVarMustBeProvided
   * @throws Exception
   */
  public function position()
  {
    /** @var ModelAbstract $modelClassName */
    $modelClassName = $this->getModelClassName();
    $model = new $modelClassName();

    if (!$model->getMeta()->hasProperty('position')) {
      throw new Exception('Model does not have position property');
    }

    $rows = $model::fetchAll($this->getConditions(), ['position' => 1], 100);

    if ($this->getRequest()->isPost()) {

      $this->getView()->setAutoRender(false);
      $this->getView()->setLayoutEnabled(false);

      foreach (($this->getParam('items') ?? []) as $index => $id) {
        $row = $model::fetchOne(['id' => $id]);
        $row->position = (int)$index;
        $row->save();
      }

      return $this->getRouter()->assemble(
        ['action' => 'index'],
        $this->getRequest()->getGetAll()
      );
    }

    $this->getView()->setVars([
      'icon' => $this->getAdminMenuItem()['icon'] ?? null,
      'title' => $this->getTitle(),

      'rows' => $rows,
      'positioning' => $this->getPositioning(),

      'params' => $this->getParams(),
      'controller' => $this->getRouter()->getController(),

      'isPositioningControl' => true
    ]);

    $this->getView()->setScript('table/position');
  }

  /**
   * @param string $id
   * @return void
   * @throws CallUndefinedMethod
   * @throws ClassWasNotFound
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   */
  public function copy(string $id): void
  {
    $this->getView()->setLayoutEnabled(false);
    $this->getView()->setAutoRender(false);

    /** @var ModelAbstract $modelClassName */
    $modelClassName = $this->getModelClassName();
    $record = $modelClassName::fetchOne(['id' => $id]);

    $data = $record->getData();
    unset($data['id']);

    if ($record->getMeta()->hasProperty('enabled')) {
      $data['enabled'] = false;
    }

    $newRecord = new $modelClassName();
    $newRecord->populateWithoutQuerying($data);
    $newRecord->save();
  }

  /**
   * @return void
   * @throws CallUndefinedMethod
   * @throws ClassWasNotFound
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   * @throws Exception
   */
  public function index()
  {
    $this->adminLog(\Air\Crud\AdminHistory\ModelAbstract::TYPE_READ_TABLE);

    $this->getView()->setVars([
      'icon' => $this->getAdminMenuItem()['icon'] ?? null,
      'title' => $this->getTitle(),
      'manageable' => $this->getManageable(),
      'positioning' => $this->getPositioning(),
      'export' => $this->getExportHeader(),

      'filter' => $this->getFilterWithValues(),
      'header' => $this->getHeader(),
      'headerButtons' => $this->getHeaderButtons(),
      'controls' => $this->getControls(),
      'paginator' => $this->getPaginator(),
      'controller' => $this->getRouter()->getController(),
    ]);

    $this->getView()->setScript('table/index');
  }

  /**
   * @return void
   * @throws Exception
   */
  public function select(): void
  {
    $this->getView()->setVars([
      'icon' => $this->getAdminMenuItem()['icon'] ?? null,
      'title' => $this->getTitle(),
      'filter' => $this->getFilterWithValues(),
      'header' => $this->getHeader(),
      'paginator' => $this->getPaginator(),
      'controller' => $this->getRouter()->getController(),
      'controls' => $this->getControls(),
      'isSelectControl' => true,
    ]);
    $this->getView()->setScript('table/index');
  }

  /**
   * @return string
   * @throws ClassWasNotFound
   * @throws Exception
   */
  public function export(): string
  {
    $this->getView()->setLayoutEnabled(false);

    ini_set('memory_limit', '-1');
    set_time_limit(0);

    $response = [];
    $header = [];
    $exportHeader = $this->getExportHeader();

    foreach ($exportHeader as $field) {
      $header[] = $field['title'];
    }

    $response[] = '"' . implode('","', $header) . '"';

    /** @var ModelAbstract $modelClassName */
    $modelClassName = $this->getModelClassName();

    /** @var ModelAbstract  $table */
    $table = $modelClassName::fetchAll(
      $this->getConditions(),
      $this->getSorting()
    );

    foreach ($table as $row) {
      $cols = [];
      foreach ($exportHeader as $col) {
        $cols[] = is_string($col['by']) ? $row->{$col['by']} : $col['by']($row);
      }
      $response[] = '"' . implode('","', $cols) . '"';
    }

    $response = implode(";\n", $response) . ';';

    $fileName = $this->getExportFileName() . '_' . date('c') . '.csv';
    $this->getResponse()->setHeader('Content-Disposition', 'attachment;filename=' . $fileName);
    $this->getResponse()->setHeader('Content-Size', (string)mb_strlen($response));

    return $response;
  }

  /**
   * @param string|null $id
   *
   * @throws DomainMustBeProvided
   * @throws RouterVarMustBeProvided
   * @throws ClassWasNotFound
   * @throws CallUndefinedMethod
   * @throws Exception
   */
  public function manage(string $id = null)
  {
    /** @var ModelAbstract $modelClassName */
    $modelClassName = $this->getModelClassName();
    $model = $modelClassName::fetchObject(['id' => $id]);

    $form = $this->getForm($model);

    if ($this->getRequest()->isPost()) {
      if ($form->isValid($this->getRequest()->getPostAll())) {

        $oldData = [];

        $formData = $form->getValues();

        foreach ($formData as $key => $value) {
          if ($model->{$key} instanceof ModelInterface) {
            $oldValue = $model->{$key}->id;
          } else {
            $oldValue = $model->{$key};
          }
          $oldData[$key] = $oldValue;
        }

        $this->adminLog(
          $model->id
            ? \Air\Crud\AdminHistory\ModelAbstract::TYPE_WRITE_ENTITY
            : \Air\Crud\AdminHistory\ModelAbstract::TYPE_CREATE_ENTITY,
          isset($oldData['title']) ? [$oldData['title']] : $oldData,
          null,
          $oldData,
          $formData
        );

        if ($model->getMeta()->hasProperty('updatedAt') && !isset($formData['updatedAt'])) {
          $formData['updatedAt'] = time();
        }

        $model->populateWithoutQuerying($formData);
        $isCreating = !!$model->id;
        $model->save();

        if ($isCreating) {
          $this->didChanged($model, $formData);
        } else {
          $this->didCreated($model, $formData);

          if ($model->getMeta()->hasProperty('createdAt') && !isset($formData['createdAt'])) {
            $model->createdAt = time();
            $model->save();
          }
        }

        $this->didSaved($model, $formData);

        $this->getView()->setLayoutEnabled(false);
        $this->getView()->setAutoRender(false);

        return [
          'url' => $this->getRouter()->assemble(
            ['controller' => $this->getEntity(), 'action' => 'manage'],
            ['returnUrl' => $this->getRequest()->getPost('return-url'), 'id' => $model->id]
          ),
          'quickSave' => $this->getParam('quick-save'),
          'newOne' => !$isCreating
        ];
      } else {
        $this->getResponse()->setStatusCode(400);
      }
    }

    if ($model->id) {
      try {
        $data = Map::execute($model, array_keys($this->getHeader()));
      } catch (\Throwable) {
        $data = ['id' => $model->id];
      }
      $this->adminLog(
        \Air\Crud\AdminHistory\ModelAbstract::TYPE_READ_ENTITY,
        $data,
      );
    }

    $returnUrl = $this->getParam('returnUrl');
    if (!$returnUrl) {
      $returnUrl = $this->getRouter()->assemble([
        'controller' => $this->getRouter()->getController(),
        'action' => 'index'
      ]);
    }

    $form->setReturnUrl($returnUrl);

    $this->getView()->setVars([
      'icon' => $this->getAdminMenuItem()['icon'] ?? null,
      'title' => $this->getTitle(),
      'form' => $form,
      'mode' => 'manage'
    ]);

    $this->getView()->setScript('form/index');
  }
}
