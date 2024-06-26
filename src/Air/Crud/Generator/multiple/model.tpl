<?php

declare(strict_types=1);

namespace {namespace};

use Air\Crud\Model\Language;
use Air\Model\ModelAbstract;
use Air\Type\File;
use Air\Type\Meta;
use Air\Type\Quote;
use Air\Type\RichContent;

/**
 * @collection {name}
 *
 * @property string $id
 * @property boolean $enabled
 * @property string $url
 *
 * @property string $title
 * @property string $subTitle
 * @property string $description
 *
 * @property integer $date
 * @property integer $dateTime
 *
 * @property File $image
 * @property File[] $images
 *
 * @property Quote $quote
 *
 * @property string $embed
 * @property string $content
 *
 * @property RichContent[] $richContent
 *
 * @property Meta $meta
 *
 * @property integer $position
 *
 * @property string $status
 *
 * @property integer $createdAt
 * @property integer $updatedAt
 *
 * @property {name} $singleModelRef
 * @property {name}[] $multipleModelRef
 *
 * @property Language $language
 */
class {name} extends ModelAbstract
{
  const STATUS_1 = 'status-1';
  const STATUS_2 = 'status-2';
}

