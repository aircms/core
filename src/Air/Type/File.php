<?php

declare(strict_types=1);

namespace Air\Type;

use Air\Core\Front;
use Air\Storage;

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

  public function getSrc(
    ?int    $width = null,
    ?int    $height = null,
    ?int    $quality = null,
    ?string $format = null
  ): string
  {
    if (str_starts_with($this->src, 'http')) {
      return $this->src;
    }

    $src = $this->src;

    $pathInfo = pathinfo($src);
    $dirname = $pathInfo['dirname'] ?? '';
    $filename = $pathInfo['filename'] ?? '';
    $ext = strtolower($pathInfo['extension'] ?? '');

    // собираем модификаторы
    $mods = [];
    if ($width !== null) $mods[] = 'w' . $width;
    if ($height !== null) $mods[] = 'h' . $height;
    if ($quality !== null) $mods[] = 'q' . $quality;

    $newFilename = $filename;

    if (!empty($mods)) {
      $newFilename .= '_mod_' . implode('_', $mods);
    }

    // формат: если передан — используем его, иначе исходное расширение
    $newExt = $format ? strtolower($format) : $ext;

    $src = array_values(array_filter(explode('/', "{$dirname}/{$newFilename}.{$newExt}")));
    $src = implode('/', $src);

    return Front::getInstance()->getConfig()['air']['storage']['url'] . $src;
  }

  public function getSrcContent(): string|false
  {
    return file_get_contents($this->getSrc());
  }

  public function isImage(): bool
  {
    return str_contains($this->getMime(), 'image');
  }

  public function remove(): bool
  {
    return Storage::deleteFile($this->path);
  }

  public static function fromArray(?array $file): self
  {
    return new self($file);
  }
}