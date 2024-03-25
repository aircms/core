<?php

namespace Air\Form\Element;

/**
 * Class DateTime
 * @package Air\Form\Element
 */
class DateTime extends ElementAbstract
{
  /**
   * @var string
   */
  public ?string $elementTemplate = 'form/element/date-time';

  /**
   * @var string
   */
  public $format = 'YYYY-MM-DD HH:mm';

  /**
   * @var string
   */
  public $phpFormat = 'Y-m-d H:i';

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
   * @return int|null
   */
  public function getValue(): ?int
  {
    $value = parent::getValue();

    if (empty($value)) {
      return 0;
    }

    if (is_string($value)) {
      return intval(strtotime($value));
    }

    return intval($value);
  }
}
