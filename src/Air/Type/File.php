<?php

declare(strict_types=1);

namespace Air\Type;

use Air\Core\Front;

class File extends TypeAbstract
{
  public int $size = 0;
  public string $mime = '';
  public string $path = '';
  public int $time = 0;
  public string $src = '';
  public string $thumbnail = '';
  public string $title = '';
  public string $alt = '';
  public array $dims = [
    'width' => 0,
    'height' => 0
  ];

  public function getSize(): int
  {
    return $this->size;
  }

  public function getMime(): string
  {
    return $this->mime;
  }

  public function getPath(): string
  {
    return $this->path;
  }

  public function getTime(): int
  {
    return $this->time;
  }

  public function getTitle(): string
  {
    return $this->title;
  }

  public function getAlt(): string
  {
    return $this->alt;
  }

  public function getDims(): array
  {
    return $this->dims;
  }

  public function getThumbnail(): string
  {
    if (str_starts_with($this->thumbnail, 'http')) {
      return $this->thumbnail;
    }
    return Front::getInstance()->getConfig()['air']['storage']['url'] . $this->thumbnail;
  }

  public function getSrc(?int $width = null, ?int $height = null, ?int $quality = null): string
  {
    if (str_starts_with($this->src, 'http')) {
      $src = $this->src;
    } else {
      $src = Front::getInstance()->getConfig()['air']['storage']['url'] . $this->src;
    }

    if (!$width && !$height && !$quality) {
      return $src;
    }

    $suffix = '';

    if ($width !== null || $height !== null) {
      $suffix .= '_r';
      if ($width !== null) {
        $suffix .= $width;
      }
      if ($height !== null) {
        $suffix .= 'x' . $height;
      }
    }

    if ($quality !== null) {
      $suffix .= '_q' . $quality;
    }

    if ($suffix === '') {
      return $src;
    }

    return preg_replace('/(\\.[^\\.\\/\\?]+)(\\?.*)?$/', $suffix . '$1$2', $src);
  }

  public function isImage(): bool
  {
    return str_contains($this->getMime(), 'image');
  }

  public static function fromArray(?array $file): self
  {
    return new self($file);
  }
}