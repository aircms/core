<?php

declare(strict_types=1);

namespace Air\Crud\Model;

use Air\Core\Exception\ClassWasNotFound;
use Air\Model\Exception\CallUndefinedMethod;
use Air\Model\Exception\ConfigWasNotProvided;
use Air\Model\Exception\DriverClassDoesNotExists;
use Air\Model\Exception\DriverClassDoesNotExtendsFromDriverAbstract;
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
 * @property integer $position
 */
class Language extends ModelAbstract
{
  /**
   * @var Language|null
   */
  private static self|null $defaultLanguage = null;

  /**
   * @param Language $language
   * @return void
   */
  public static function setDefaultLanguage(self $language): void
  {
    self::$defaultLanguage = $language;
  }

  /**
   * @return mixed|self|null
   * @throws ClassWasNotFound
   * @throws CallUndefinedMethod
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   */
  public static function getLanguage(): mixed
  {
    if (self::$defaultLanguage) {
      return self::$defaultLanguage;
    }
    self::$defaultLanguage = self::one([
      'isDefault' => true
    ]);
    return self::$defaultLanguage;
  }
}