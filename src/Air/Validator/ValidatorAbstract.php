<?php

namespace Air\Validator;

/**
 * Class ValidatorAbstract
 * @package Air\Validator
 */
abstract class ValidatorAbstract
{
  /**
   * @var bool
   */
  public $allowNull = false;

  /**
   * ValidatorAbstract constructor.
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
   * @param $value
   * @return bool
   */
  public abstract function isValid($value): bool;
}
