<?php

declare(strict_types=1);

namespace Air\Form\Element;

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