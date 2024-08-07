<?php

declare(strict_types=1);

namespace Air\Validator;

abstract class ValidatorAbstract
{
  /**
   * @var bool
   */
  public bool $allowNull = false;

  /**
   * @param array $options
   */
  public function __construct(array $options = [])
  {
    foreach ($options as $name => $value) {

      if (is_callable([$this, 'set' . ucfirst($name)])) {
        call_user_func_array([$this, 'set' . ucfirst($name)], [$value]);
      }
    }
  }

  /**
   * @return bool
   */
  public function isAllowNull(): bool
  {
    return $this->allowNull;
  }

  /**
   * @param bool $allowNull
   */
  public function setAllowNull(bool $allowNull): void
  {
    $this->allowNull = $allowNull;
  }

  /**
   * @param mixed $value
   * @param array $options
   * @return bool
   */
  public static function valid(mixed $value, array $options = []): bool
  {
    $validator = new static($options);
    return $validator->isValid($value);
  }

  /**
   * @param $value
   * @return bool
   */
  public abstract function isValid($value): bool;
}
