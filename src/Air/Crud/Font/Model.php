<?php

namespace Air\Crud\Font;

use Air\Model\ModelAbstract;
use Air\Type\File;

/**
 * @collection AirFont
 *
 * @property string $id
 *
 * @property string $title
 *
 * @property File $eotIe9
 * @property File $eotIe6Ie8
 * @property File $woff2
 * @property File $woff
 * @property File $ttf
 * @property File $svg
 *
 * @property boolean $enabled
 */
class Model extends ModelAbstract
{
}