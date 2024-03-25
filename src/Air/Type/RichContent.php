<?php

declare(strict_types=1);

namespace Air\Type;

class RichContent
{
  const TYPE_FILE = 'file';
  const TYPE_FILES = 'files';
  const TYPE_QUOTE = 'quote';
  const TYPE_TEXT = 'text';
  const TYPE_HTML = 'html';
  const TYPE_EMBED = 'embed';

  /**
   * @var string
   */
  public string $type;

  /**
   * @var File|File[]|Quote[]|string
   */
  public mixed $value;

  /**
   * @param array|null $data
   */
  public function __construct(?array $data = null)
  {
    $this->type = $data['type'] ?? 'empty';

    switch ($this->type) {

      case self::TYPE_FILE:
        $this->value = new File($data['value'] ?? []);
        break;

      case self::TYPE_FILES:
        $this->value = [];
        foreach (($data['value'] ?? []) as $file) {
          $this->value[] = new File($file);
        }
        break;

      case self::TYPE_QUOTE:
        $this->value = new Quote($data['value'] ?? []);
        break;

      case self::TYPE_EMBED:
      case self::TYPE_TEXT:
      case self::TYPE_HTML:
        $this->value = $data['value'] ?? '';
        break;
    }
  }

  /**
   * @return string
   */
  public function getType(): string
  {
    return $this->type;
  }

  /**
   * @return File|File[]|Quote|string
   */
  public function getValue(): mixed
  {
    return $this->value;
  }

  /**
   * @return array
   */
  public function toArray(): array
  {
    if ($this->type === self::TYPE_HTML || $this->type === self::TYPE_TEXT || $this->type === self::TYPE_EMBED) {
      $value = $this->value;
    } else {
      $value = (array)$this->value;
    }

    return [
      'type' => $this->type,
      'value' => $value
    ];
  }
}
