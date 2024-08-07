<?php

declare(strict_types=1);

namespace Air\Type;

use Air\Core\Exception\ClassWasNotFound;
use Air\Core\Front;
use Air\Model\Exception\CallUndefinedMethod;
use Air\Model\Exception\ConfigWasNotProvided;
use Air\Model\Exception\DriverClassDoesNotExists;
use Air\Model\Exception\DriverClassDoesNotExtendsFromDriverAbstract;
use ReflectionException;
use Throwable;

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
    if (str_starts_with($this->thumbnail, 'http')) {
      return $this->thumbnail;
    }
    return Front::getInstance()->getConfig()['air']['storage']['url'] . $this->thumbnail;
  }

  /**
   * @return string
   * @throws ClassWasNotFound
   */
  public function getSrc(): string
  {
    if (str_starts_with($this->src, 'http')) {
      return $this->src;
    }
    return Front::getInstance()->getConfig()['air']['storage']['url'] . $this->src;
  }

  /**
   * @return bool
   */
  public function isImage(): bool
  {
    return str_contains($this->getMime(), 'image');
  }

  /**
   * @param array|null $file
   * @return self
   * @throws ClassWasNotFound
   * @throws CallUndefinedMethod
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   * @throws ReflectionException
   * @throws Throwable
   */
  public static function fromArray(?array $file): self
  {
    return new self($file);
  }
}