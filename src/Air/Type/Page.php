<?php

declare(strict_types=1);

namespace Air\Type;

use Exception;
use Air\Core\Exception\ClassWasNotFound;

class Page
{
  const WIDTH = 360;
  const HEIGHT = 560;
  const GUTTER = 10;

  /**
   * @var string|null
   */
  public ?string $backgroundColor = null;

  /**
   * @var File|null
   */
  public ?File $backgroundImage = null;

  /**
   * @var int
   */
  public int $width = self::WIDTH;

  /**
   * @var int
   */
  public int $height = self::HEIGHT;

  /**
   * @var PageItem[]|null
   */
  public ?array $items = [];

  /**
   * @var int
   */
  public int $gutter = self::GUTTER;

  /**
   * @return string|null
   */
  public function getBackgroundColor(): ?string
  {
    return $this->backgroundColor;
  }

  /**
   * @return File|null
   */
  public function getBackgroundImage(): ?File
  {
    return $this->backgroundImage;
  }

  /**
   * @return array|PageItem[]|null
   */
  public function getItems(): ?array
  {
    return $this->items;
  }

  /**
   * @return int
   */
  public function getWidth(): int
  {
    return $this->width;
  }

  /**
   * @return int
   */
  public function getHeight(): int
  {
    return $this->height;
  }

  /**
   * @return int
   */
  public function getGutter(): int
  {
    return $this->gutter;
  }

  /**
   * @param array|null $page
   * @throws Exception
   */
  public function __construct(?array $page = [])
  {
    if ($page['backgroundColor'] ?? false) {
      $this->backgroundColor = $page['backgroundColor'];
    }

    if ($page['backgroundImage'] ?? false) {
      $this->backgroundImage = new File($page['backgroundImage']);
    }

    if ($page['width'] ?? false) {
      $this->width = $page['width'];
    }

    if ($page['height'] ?? false) {
      $this->height = $page['height'];
    }

    if ($page['gutter'] ?? false) {
      $this->gutter = $page['gutter'];
    }

    foreach (($page['items'] ?? []) as $item) {
      $this->items[] = new PageItem($item);
    }
  }

  /**
   * @return string
   * @throws ClassWasNotFound
   */
  public function asCss(): string
  {
    return
      ($this->getBackgroundImage() ? "background-image: url('" . $this->getBackgroundImage()->getSrc() . "');" : '') .
      ($this->getBackgroundColor() ? "background-color: " . $this->getBackgroundColor() . ";" : '') .
      ('width: ' . $this->getWidth() . 'px; height: ' . $this->getHeight() . 'px');
  }
}