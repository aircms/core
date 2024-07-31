<?php

declare(strict_types=1);

namespace Air\Crud\Model;

use Air\Core\Exception\ClassWasNotFound;
use Air\Model\Exception\CallUndefinedMethod;
use Air\Model\Exception\ConfigWasNotProvided;
use Air\Model\Exception\DriverClassDoesNotExists;
use Air\Model\Exception\DriverClassDoesNotExtendsFromDriverAbstract;
use Air\Model\Meta\Exception\CollectionCantBeWithoutPrimary;
use Air\Model\Meta\Exception\CollectionCantBeWithoutProperties;
use Air\Model\Meta\Exception\CollectionNameDoesNotExists;
use Air\Model\Meta\Exception\PropertyIsSetIncorrectly;
use Air\Model\ModelAbstract;
use ReflectionException;

/**
 * @collection AirPhrase
 *
 * @property string $id
 *
 * @property string $key
 * @property string $value
 * @property boolean $isEdited
 *
 * @property Language $language
 */
class Phrase extends ModelAbstract
{
  /**
   * @var array|null
   */
  private static ?array $phrases = null;

  /**
   * @param string $key
   * @return string
   * @throws CallUndefinedMethod
   * @throws ClassWasNotFound
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   * @throws CollectionCantBeWithoutPrimary
   * @throws CollectionCantBeWithoutProperties
   * @throws CollectionNameDoesNotExists
   * @throws PropertyIsSetIncorrectly
   * @throws ReflectionException
   */
  public static function t(string $key): string
  {
    if (!self::$phrases) {
      self::$phrases = [];

      foreach (self::all() as $phrase) {
        $phraseData = $phrase->getData();
        self::$phrases[$phraseData['key'] . $phraseData['language']] = $phraseData['value'];
      }
    }

    $languageData = Language::getLanguage()->getData();
    $key = trim($key);

    if (isset(self::$phrases[$key . $languageData['id']])) {
      return self::$phrases[$key . $languageData['id']];
    }

    foreach (Language::all() as $language) {
      $phrase = new self([
        'key' => $key,
        'value' => $key,
        'language' => $language->id,
        'isEdited' => $language->isDefault
      ]);
      $phrase->save();
    }
    return $key;
  }
}