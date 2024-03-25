<?php

namespace Air\Form\Element;

/**
 * Class Date
 * @package Air\Form\Element
 */
class Date extends ElementAbstract
{
  /**
   * @var string
   */
  public ?string $elementTemplate = 'form/element/date';

  /**
   * @var string
   */
  public $format = 'YYYY-MM-DD';

  /**
   * @var string
   */
  public $phpFormat = 'Y-m-d';

  /**
   * @return string
   */
  public function getFormat(): string
  {
    return $this->format;
  }

  /**
   * @param string $format
   */
  public function setFormat(string $format): void
  {
    $this->format = $format;
  }

  /**
   * @return string
   */
  public function getPhpFormat(): string
  {
    return $this->phpFormat;
  }

  /**
   * @param string $phpFormat
   */
  public function setPhpFormat(string $phpFormat): void
  {
    $this->phpFormat = $phpFormat;
  }

  /**
   * @return int
   */
  public function getValue(): int
  {
    $value = parent::getValue();

    if (is_string($value) && strlen($value)) {
      return strtotime($value);
    }

    return intval($value) ?? 0;
  }
}
