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
  private static self|null $defaultLanguage = null;

  public static function setDefaultLanguage(self $language): void
  {
    self::$defaultLanguage = $language;
  }

  public static function getLanguage(): ?self
  {
    if (self::$defaultLanguage) {
      return self::$defaultLanguage;
    }
    self::$defaultLanguage = self::singleOne([
      'isDefault' => true
    ]);
    return self::$defaultLanguage;
  }
}