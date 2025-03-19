<?php

declare(strict_types=1);

namespace Air\Crud\Model;

use Air\Model\ModelAbstract;

/**
 * @collection EmailTemplate
 *
 * @property string $id
 *
 * @property string $url
 * @property string $subject
 * @property string $body
 *
 * @property Language $language
 * @property boolean $enabled
 */
class EmailTemplate extends ModelAbstract
{
  public static function tpl(string $templateUrl): self
  {
    return self::one(['url' => $templateUrl]);
  }
}