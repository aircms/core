<?php

declare(strict_types=1);

namespace Air\Validator;

class Phone extends ValidatorAbstract
{
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

    return strlen(trim($value)) >= 10;
  }

  /**
   * @param string $phone
   * @return string
   */
  public function normalizePhoneNumber(string $phone): string
  {
    return '+' . trim(str_replace(['+', '-', ' ', '-', '(', ')', '.', ','], '', $phone));
  }
}
