<?php

declare(strict_types=1);

namespace Air\Form\Element;

use Air\Core\Exception\ClassWasNotFound;
use Air\Core\Front;
use Air\Crud\Locale;
use Air\Form\Exception\FilterClassWasNotFound;
use Air\Form\Exception\ValidatorClassWasNotFound;
use Throwable;

class RichContent extends ElementAbstract
{
  /**
   * @var string|null
   */
  public ?string $elementTemplate = 'form/element/rich-content';

  /**
   * @param $value
   * @return bool
   * @throws FilterClassWasNotFound
   * @throws ValidatorClassWasNotFound
   * @throws ClassWasNotFound
   */
  public function isValid($value): bool
  {
    $isValid = parent::isValid($value);

    if (!$isValid) {
      $this->errorMessages = [Locale::t('Could not be empty')];
      return false;
    }

    if ((!$value || !count($value)) && !$this->isAllowNull()) {
      return false;
    }

    return true;
  }

  /**
   * @return array|string[]
   * @throws ClassWasNotFound
   */
  public function getEnabledElements(): array
  {
    $enabled = ['file', 'files', 'quote', 'text', 'html', 'embed'];
    $richContentEnabledElements = (Front::getInstance()->getConfig()['air']['admin']['rich-content'] ?? []);

    if (count($richContentEnabledElements)) {
      $enabled = $richContentEnabledElements;
    }

    return $enabled;
  }

  /**
   * @return \Air\Type\RichContent[]
   */
  public function getValue(): array
  {
    $value = parent::getValue();

    if (!$value) {
      return [];
    }

    $value = array_values($value);
    $formatterValue = [];

    if (count($value)) {
      foreach ($value as $item) {
        if (!($item instanceof \Air\Type\RichContent)) {
          if ($item['type'] === \Air\Type\RichContent::TYPE_FILE || $item['type'] === \Air\Type\RichContent::TYPE_FILES) {
            try {

              if (is_string($item['value'])) {
                $itemArray = json_decode($item['value'], true);
              } else {
                $itemArray = $item['value'];
              }

              $formatterValue[] = new \Air\Type\RichContent([
                'type' => $item['type'],
                'value' => $itemArray
              ]);
            } catch (Throwable) {
            }
          } else {
            $formatterValue[] = new \Air\Type\RichContent($item);
          }
        } else {
          $formatterValue[] = $item;
        }
      }
    }
    return $formatterValue;
  }
}