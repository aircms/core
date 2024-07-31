<?php

declare(strict_types=1);

namespace Air;

class Paging
{
  /**
   * @param int $totalCount
   * @param int $currentPage
   * @param int $itemsPerPage
   * @param int $range
   * @return array|bool
   */
  public static function paging(
    int $totalCount,
    int $currentPage = 1,
    int $itemsPerPage = 20,
    int $range = 5
  ): array|bool
  {
    if ($totalCount <= $itemsPerPage) {
      return false;
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

      $pages = array_slice($pages, intval($start), intval($range));
    }

    return [
      'prev' => $prev,
      'next' => $next,
      'pages' => $pages,
      'page' => $currentPage
    ];
  }
}