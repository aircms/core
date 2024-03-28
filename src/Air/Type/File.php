<?php

declare(strict_types=1);

namespace Air\Type;

use Air\Core\Exception\ClassWasNotFound;
use Air\Core\Front;

class File extends TypeAbstract
{
  /**
   * @var int
   */
  public int $size = 0;

  /**
   * @var string
   */
  public string $mime = '';

  /**
   * @var string
   */
  public string $path = '';

  /**
   * @var int
   */
  public int $time = 0;

  /**
   * @var string
   */
  public string $src = '';

  /**
   * @var string
   */
  public string $thumbnail = '';

  /**
   * @var string
   */
  public string $title = '';

  /**
   * @var string
   */
  public string $alt = '';

  /**
   * @var array|int[]
   */
  public array $dims = [
    'width' => 0,
    'height' => 0
  ];

  /**
   * @return int
   */
  public function getSize(): int
  {
    return $this->size;
  }

  /**
   * @return string
   */
  public function getMime(): string
  {
    return $this->mime;
  }

  /**
   * @return string
   */
  public function getPath(): string
  {
    return $this->path;
  }

  /**
   * @return int
   */
  public function getTime(): int
  {
    return $this->time;
  }

  /**
   * @return string
   */
  public function getTitle(): string
  {
    return $this->title;
  }

  /**
   * @return string
   */
  public function getAlt(): string
  {
    return $this->alt;
  }

  /**
   * @return array|int[]
   */
  public function getDims(): array
  {
    return $this->dims;
  }

  /**
   * @return string
   * @throws ClassWasNotFound
   */
  public function getThumbnail(): string
  {
    return Front::getInstance()->getConfig()['air']['storage']['url'] . $this->thumbnail;
  }

  /**
   * @return string
   * @throws ClassWasNotFound
   */
  public function getSrc(): string
  {
    return Front::getInstance()->getConfig()['air']['storage']['url'] . $this->src;
  }

  /**
   * @return bool
   */
  public function isImage(): bool
  {
    return str_contains($this->getMime(), '/image');
  }
}