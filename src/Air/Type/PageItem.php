<?php

declare(strict_types=1);

namespace Air\Type;

use Exception;
use Air\Core\Exception\ClassWasNotFound;

class PageItem
{
  const string TYPE_FILE = 'file';
  const string TYPE_HTML = 'html';
  const string TYPE_EMBED = 'embed';

  /**
   * @var string
   */
  public string $type = '';

  /**
   * @var int
   */
  public int $width = 0;

  /**
   * @var int
   */
  public int $height = 0;

  /**
   * @var int
   */
  public int $x = 0;

  /**
   * @var int
   */
  public int $y = 0;

  /**
   * @var int
   */
  public int $deep = 0;

  /**
   * @var File|array{html: string, color: string}|string
   */
  public mixed $value = null;

  /**
   * @var int
   */
  public int $transparent = 100;

  /**
   * @return string
   */
  public function getType(): string
  {
    return $this->type;
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
  public function getX(): int
  {
    return $this->x;
  }

  /**
   * @return int
   */
  public function getY(): int
  {
    return $this->y;
  }

  /**
   * @return File|array{html: string, color: string}|string
   */
  public function getValue(): mixed
  {
    return $this->value;
  }

  /**
   * @return int
   */
  public function getDeep(): int
  {
    return $this->deep;
  }

  /**
   * @return int
   */
  public function getTransparent(): int
  {
    return $this->transparent;
  }

  /**
   * @param array|null $item
   * @throws Exception
   */
  public function __construct(?array $item = [])
  {
    if (!in_array(($item['type'] ?? false), [self::TYPE_FILE, self::TYPE_HTML, self::TYPE_EMBED])) {
      throw new Exception('Unknown page item type: ' . $item['type']);
    }

    $this->value = match ($item['type'] ?? false) {
      self::TYPE_FILE => new File($item['value'] ?? []),
      self::TYPE_EMBED => $item['value'] ?? null,
      self::TYPE_HTML => $item['value'] ?? [],
      default => throw new Exception('Unknown page item type: ' . $item['type']),
    };

    $this->type = $item['type'];

    if ($item['width'] ?? false) {
      $this->width = $item['width'];
    }

    if ($item['height'] ?? false) {
      $this->height = $item['height'];
    }

    if ($item['x'] ?? false) {
      $this->x = $item['x'];
    }

    if ($item['y'] ?? false) {
      $this->y = $item['y'];
    }

    if ($item['deep'] ?? false) {
      $this->deep = $item['deep'];
    }

    if ($item['transparent'] ?? false) {
      $this->transparent = $item['transparent'];
    }
  }

  /**
   * @return string
   * @throws ClassWasNotFound
   */
  public function asCss(): string
  {
    $css =
      'left: ' . $this->getX() . 'px; top: ' . $this->getY() . 'px;' .
      'width: ' . $this->getWidth() . 'px; height: ' . $this->getHeight() . 'px;' .
      'z-index: ' . $this->getDeep() . ';' .
      'opacity: ' . ($this->getTransparent() / 100) . ';';

    if ($this->value instanceof File) {
      if ($this->value->isImage()) {
        $css .= "background-image: url('" . $this->getValue()->getSrc() . "');";
      } else {
        $css .= "background-image: url('" . $this->getValue()->getThumbnail() . "');";
      }
    }

    return $css;
  }
}