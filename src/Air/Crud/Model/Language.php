<?php

declare(strict_types=1);

namespace Air\Crud\Model;

use Air\Model\ModelAbstract;
use Air\Type\File;

/**
 * @collection AirLanguage
 *
 * @property string $id
 *
 * @property string $title
 * @property string $key
 * @property File $image
 * @property boolean $isDefault
 * @property boolean $enabled
 */
class Language extends ModelAbstract
{
  /**
   * @var Language|null
   */
  private static self|null $defaulLanguage = null;

  /**
   * @param Language $language
   * @return void
   */
  public static function setDefaultLanguage(self $language): void
  {
    self::$defaulLanguage = $language;
  }

  /**
   * @return mixed|self|null
   * @throws \Air\Core\Exception\ClassWasNotFound
   * @throws \Air\Model\Exception\CallUndefinedMethod
   * @throws \Air\Model\Exception\ConfigWasNotProvided
   * @throws \Air\Model\Exception\DriverClassDoesNotExists
   * @throws \Air\Model\Exception\DriverClassDoesNotExtendsFromDriverAbstract
   */
  public static function getLanguage(): mixed
  {
    if (self::$defaulLanguage) {
      return self::$defaulLanguage;
    }

    self::$defaulLanguage = self::one([
      'isDefault' => true
    ]);

    return self::$defaulLanguage;
  }
}