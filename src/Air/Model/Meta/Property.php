<?php

declare(strict_types=1);

namespace Air\Model\Meta;

class Property
{
  /**
   * @var string|null
   */
  private ?string $type = null;

  /**
   * @var string|null
   */
  private ?string $name = null;

  /**
   * @return string
   */
  public function getType(): string
  {
    return $this->type;
  }

  /**
   * @param string $type
   */
  public function setType(string $type): void
  {
    $this->type = $type;
  }

  /**
   * @return string
   */
  public function getName(): string
  {
    return $this->name;
  }

  /**
   * @param string $name
   */
  public function setName(string $name): void
  {
    $this->name = $name;
  }
}