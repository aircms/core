<?php

declare(strict_types=1);

namespace Air\Filter;

/**
 * Class StripTags
 * @package Air\Filter
 */
class StripTags extends FilterAbstract
{
  /**
   * @var array
   */
  public array $allowableTags = [];

  /**
   * @return array
   */
  public function getAllowableTags(): array
  {
    return $this->allowableTags;
  }

  /**
   * @param array $allowableTags
   */
  public function setAllowableTags(array $allowableTags): void
  {
    $this->allowableTags = $allowableTags;
  }

  /**
   * @param $value
   * @return string
   */
  public function filter($value)
  {
    $allowableTags = [];

    foreach ($this->allowableTags as $allowableTag) {
      $allowableTags[] = '<' . $allowableTag . '>';
    }

    return strip_tags($value, implode('', $allowableTags));
  }
}