<?php

declare(strict_types=1);

namespace Air\Validator;

class Regex extends ValidatorAbstract
{
  /**
   * @var string
   */
  public string $pattern = '';

  /**
   * @return string
   */
  public function getPattern(): string
  {
    return $this->pattern;
  }

  /**
   * @param string $pattern
   */
  public function setPattern(string $pattern): void
  {
    $this->pattern = $pattern;
  }

  /**
   * @param string $value
   * @return bool
   */
  public function isValid($value): bool
  {
    $value = $value ?? '';

    if (empty($value) && $this->allowNull) {
      return true;
    }

    return !!preg_match($this->pattern, $value);
  }
}