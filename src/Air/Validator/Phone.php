<?php

namespace Air\Validator;

/**
 * Class Phone
 * @package Air\Validator
 */
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

    return strlen(trim($value)) >= 11;
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
