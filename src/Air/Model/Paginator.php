<?php

declare(strict_types=1);

namespace Air\Model;

use Air\Core\Exception\ClassWasNotFound;
use Air\Model\Driver\CursorAbstract;
use Air\Model\Exception\CallUndefinedMethod;
use Air\Model\Exception\ConfigWasNotProvided;
use Air\Model\Exception\DriverClassDoesNotExists;
use Air\Model\Exception\DriverClassDoesNotExtendsFromDriverAbstract;

class Paginator
{
  /**
   * @var ModelAbstract |null
   */
  private ?ModelAbstract $model;

  /**
   * @var array|null
   */
  private ?array $cond;

  /**
   * @var array|null
   */
  private ?array $sort;

  /**
   * @var int
   */
  private int $page = 0;

  /**
   * @var int
   */
  private int $itemsPerPage = 10;

  /**
   * @param ModelAbstract $model
   * @param array|null $cond
   * @param array|null $sort
   */
  public function __construct(ModelAbstract $model, array $cond = null, array $sort = null)
  {
    $this->model = $model;

    $this->cond = $cond;
    $this->sort = $sort;
  }

  /**
   * @return CursorAbstract|array
   * @throws ClassWasNotFound
   * @throws CallUndefinedMethod
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   */
  public function getItems(): CursorAbstract|array
  {
    /** @var ModelAbstract $modelClassName */
    $modelClassName = get_class($this->model);

    $limit = $this->getItemsPerPage();
    $offset = ($this->getPage() - 1) * $limit;

    return $modelClassName::fetchAll($this->cond, $this->sort, $limit, $offset);
  }

  /**
   * @return int
   */
  public function getItemsPerPage(): int
  {
    return $this->itemsPerPage;
  }

  /**
   * @param int $itemsPerPage
   */
  public function setItemsPerPage(int $itemsPerPage): void
  {
    $this->itemsPerPage = $itemsPerPage;
  }

  /**
   * @return int
   */
  public function getPage(): int
  {
    return $this->page;
  }

  /**
   * @param int $page
   */
  public function setPage(int $page): void
  {
    $this->page = $page;
  }

  /**
   * @return int
   * @throws CallUndefinedMethod
   * @throws ClassWasNotFound
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   */
  public function count(): int
  {
    /** @var ModelAbstract $modelClassName */
    $modelClassName = get_class($this->model);
    return $modelClassName::count($this->cond);
  }

  /**
   * @return array|null
   * @throws CallUndefinedMethod
   * @throws ClassWasNotFound
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   */
  public function calculate(): ?array
  {
    $totalCount = $this->model::count($this->cond);
    $currentPage = $this->page;
    $itemsPerPage = $this->itemsPerPage;
    $range = 5;

    if (!$totalCount) {
      return null;
    }

    $prev = $currentPage != 1 ? $currentPage - 1 : false;
    $totalPages = intval(ceil($totalCount / $itemsPerPage));
    $next = $currentPage < $totalPages ? $currentPage + 1 : false;

    $pages = range(1, $totalPages);

    if ($totalPages > $range) {

      $start = $currentPage - ceil($range / 2);

      if ($start < 0) {
        $start = 0;
      }

      if ($start + $range > $totalPages) {
        $start = $totalPages - $range;
      }

      $pages = array_slice($pages, intval($start), $range);
    }

    return [
      'prev' => $prev,
      'pages' => $pages,
      'next' => $next,
    ];
  }
}
