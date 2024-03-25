<?php

declare(strict_types=1);

namespace Air\Form\Element;

abstract class PageAbstract extends ElementAbstract
{
  /**
   * @var array|array[]
   */
  public array $sizes = [
    [
      'title' => 'A4 portrait',
      'size' => [
        'width' => \Air\Type\Page::WIDTH,
        'height' => \Air\Type\Page::HEIGHT,
      ],
    ],
    [
      'title' => 'A4 landscape',
      'size' => [
        'width' => \Air\Type\Page::HEIGHT,
        'height' => \Air\Type\Page::WIDTH,
      ],
    ]
  ];

  /**
   * @var int[]
   */
  public array $gutters = [5, 10, 15, 20, 25, 30, 35, 40, 45, 50];

  /**
   * @return array|array[]
   */
  public function getSizes(): array
  {
    return $this->sizes;
  }

  /**
   * @param array $sizes
   * @return void
   */
  public function setSizes(array $sizes): void
  {
    $this->sizes = $sizes;
  }

  /**
   * @return int[]
   */
  public function getGutters(): array
  {
    return $this->gutters;
  }

  /**
   * @param array $gutters
   * @return void
   */
  public function setGutters(array $gutters): void
  {
    $this->gutters = $gutters;
  }
}