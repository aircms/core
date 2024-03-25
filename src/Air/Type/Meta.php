<?php

declare(strict_types=1);

namespace Air\Type;

class Meta
{
  /**
   * @var string
   */
  public string $title = '';

  /**
   * @var string
   */
  public string $description = '';

  /**
   * @var string
   */
  public string $keywords = '';

  /**
   * @var string
   */
  public string $ogTitle = '';

  /**
   * @var string
   */
  public string $ogDescription = '';

  /**
   * @var File|null
   */
  public ?File $ogImage = null;

  /**
   * @param array|null $quote
   */
  public function __construct(?array $quote = [])
  {
    foreach (array_keys(get_class_vars(self::class)) as $var) {
      if (!empty($quote[$var])) {
        if ($var === 'ogImage') {
          $this->{$var} = new File((array)$quote[$var]);
        } else {
          $this->{$var} = $quote[$var];
        }
      }
    }
  }

  /**
   * @return string
   */
  public function getTitle(): string
  {
    return $this->title;
  }

  /**
   * @return string
   */
  public function getDescription(): string
  {
    return $this->description;
  }

  /**
   * @return string
   */
  public function getKeywords(): string
  {
    return $this->keywords;
  }

  /**
   * @return string
   */
  public function getOgTitle(): string
  {
    return $this->ogTitle;
  }

  /**
   * @return string
   */
  public function getOgDescription(): string
  {
    return $this->ogDescription;
  }

  /**
   * @return File|null
   */
  public function getOgImage(): ?File
  {
    return $this->ogImage;
  }
}