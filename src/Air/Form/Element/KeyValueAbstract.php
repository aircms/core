<?php

declare(strict_types=1);

namespace Air\Form\Element;

use Air\Core\Exception\ClassWasNotFound;
use Air\Crud\Locale;

abstract class KeyValueAbstract extends ElementAbstract
{
  /**
   * @var string
   */
  public string $keyLabel = 'Key';

  /**
   * @var string
   */
  public string $valueLabel = 'Value';

  /**
   * @var string
   */
  public string $keyPropertyName = 'key';

  /**
   * @var string
   */
  public string $valuePropertyName = 'value';

  /**
   * @param string $name
   * @param array $userOptions
   * @throws ClassWasNotFound
   */
  public function __construct(string $name, array $userOptions = [])
  {
    $this->keyLabel = Locale::t('Key');
    $this->valueLabel = Locale::t('Label');

    parent::__construct($name, $userOptions);
  }

  /**
   * @return string
   */
  public function getKeyLabel(): string
  {
    return $this->keyLabel;
  }

  /**
   * @param string $keyLabel
   */
  public function setKeyLabel(string $keyLabel): void
  {
    $this->keyLabel = $keyLabel;
  }

  /**
   * @return string
   */
  public function getValueLabel(): string
  {
    return $this->valueLabel;
  }

  /**
   * @param string $valueLabel
   */
  public function setValueLabel(string $valueLabel): void
  {
    $this->valueLabel = $valueLabel;
  }

  /**
   * @return string
   */
  public function getKeyPropertyName(): string
  {
    return $this->keyPropertyName;
  }

  /**
   * @param string $keyPropertyName
   * @return void
   */
  public function setKeyPropertyName(string $keyPropertyName): void
  {
    $this->keyPropertyName = $keyPropertyName;
  }

  /**
   * @return string
   */
  public function getValuePropertyName(): string
  {
    return $this->valuePropertyName;
  }

  /**
   * @param string $valuePropertyName
   * @return void
   */
  public function setValuePropertyName(string $valuePropertyName): void
  {
    $this->valuePropertyName = $valuePropertyName;
  }
}