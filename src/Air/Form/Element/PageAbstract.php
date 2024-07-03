<?php

declare(strict_types=1);

namespace Air\Form\Element;

use Air\Crud\Locale;

abstract class PageAbstract extends ElementAbstract
{
  /**
   * @var array|array[]
   */
  public array $sizes = [
    [
      'title' => '',
      'size' => [
        'width' => \Air\Type\Page::WIDTH,
        'height' => \Air\Type\Page::HEIGHT,
      ],
    ],
    [
      'title' => '',
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
   * @param string $name
   * @param array $userOptions
   */
  public function __construct(string $name, array $userOptions = [])
  {
    $this->sizes[0]['title'] = Locale::t('A4 portrait');
    $this->sizes[1]['title'] = Locale::t('A4 landscape');

    parent::__construct($name, $userOptions);
  }

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