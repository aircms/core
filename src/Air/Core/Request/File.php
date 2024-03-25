<?php

declare(strict_types=1);

namespace Air\Core\Request;

class File
{
  /**
   * @var string|null
   */
  public ?string $name = null;

  /**
   * @var string|null
   */
  public ?string $type = null;

  /**
   * @var string|null
   */
  public ?string $tmpName = null;

  /**
   * @var int
   */
  public int $error = 0;

  /**
   * @var int
   */
  public int $size = 0;

  /**
   * File constructor.
   * @param array $options
   */
  public function __construct(array $options = [])
  {
    foreach ($options as $key => $value) {
      if (is_callable([$this, 'set' . ucfirst($key)])) {
        $this->{'set' . ucfirst($key)}($value);
      }
    }
  }

  /**
   * @return string|null
   */
  public function getName(): ?string
  {
    return $this->name;
  }

  /**
   * @param string|null $name
   * @return void
   */
  public function setName(?string $name): void
  {
    $this->name = $name;
  }

  /**
   * @return string|null
   */
  public function getType(): ?string
  {
    return $this->type;
  }

  /**
   * @param string|null $type
   * @return void
   */
  public function setType(?string $type): void
  {
    $this->type = $type;
  }

  /**
   * @return string|null
   */
  public function getTmpName(): ?string
  {
    return $this->tmpName;
  }

  /**
   * @param string|null $tmpName
   * @return void
   */
  public function setTmpName(?string $tmpName): void
  {
    $this->tmpName = $tmpName;
  }

  /**
   * @return int
   */
  public function getError(): int
  {
    return $this->error;
  }

  /**
   * @param int $error
   */
  public function setError(int $error): void
  {
    $this->error = $error;
  }

  /**
   * @return int
   */
  public function getSize(): int
  {
    return $this->size;
  }

  /**
   * @param int $size
   */
  public function setSize(int $size): void
  {
    $this->size = $size;
  }

  /**
   * @return array
   */
  public function toArray(): array
  {
    return [
      'name' => $this->getName(),
      'type' => $this->getType(),
      'tmpName' => $this->getTmpName(),
      'error' => $this->getError(),
      'size' => $this->getSize()
    ];
  }
}
