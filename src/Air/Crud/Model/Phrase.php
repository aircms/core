<?php

declare(strict_types=1);

namespace Air\Crud\Model;

use Air\Model\ModelAbstract;

/**
 * @collection AirPhrase
 *
 * @property string $id
 *
 * @property string $key
 * @property string $value
 *
 * @property Language $language
 */
class Phrase extends ModelAbstract
{
  /**
   * @param string $key
   * @return string
   */
  public static function t(string $key): string
  {
    $language = Language::getLanguage();
    $key = trim($key);

    $phrase = self::fetchOne([
      'language' => $language,
      'key' => $key,
    ]);

    if (!$phrase) {
      foreach (Language::all() as $language) {
        $phrase = new self;
        $phrase->key = $key;
        $phrase->value = $key;
        $phrase->language = $language;
        $phrase->save();
      }
      return $key;
    }
    return $phrase->value;
  }
}