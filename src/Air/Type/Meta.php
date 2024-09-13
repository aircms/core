<?php

declare(strict_types=1);

namespace Air\Type;

use Air\Core\Exception\ClassWasNotFound;
use Air\Model\Exception\CallUndefinedMethod;
use Air\Model\Exception\ConfigWasNotProvided;
use Air\Model\Exception\DriverClassDoesNotExists;
use Air\Model\Exception\DriverClassDoesNotExtendsFromDriverAbstract;
use Air\Model\Meta\Exception\PropertyWasNotFound;
use Air\Model\ModelAbstract;
use ReflectionException;
use Throwable;

class Meta
{
  /**
   * @var string
   */
  public string $title = '';

  /**
   * @var string
   */
  public string $description = '';

  /**
   * @var string
   */
  public string $ogTitle = '';

  /**
   * @var string
   */
  public string $ogDescription = '';

  /**
   * @var File|null
   */
  public ?File $ogImage = null;

  /**
   * @var bool
   */
  public bool $useModelData = false;

  /**
   * @var string
   */
  public string $modelClassName = '';

  /**
   * @var string
   */
  public string $modelObjectId = '';

  /**
   * @return string
   */
  public function getTitle(): string
  {
    return $this->title;
  }

  /**
   * @return string
   */
  public function getDescription(): string
  {
    return $this->description;
  }

  /**
   * @return string
   */
  public function getOgTitle(): string
  {
    return $this->ogTitle;
  }

  /**
   * @return string
   */
  public function getOgDescription(): string
  {
    return $this->ogDescription;
  }

  /**
   * @return File|null
   */
  public function getOgImage(): ?File
  {
    return $this->ogImage;
  }

  /**
   * @return bool
   */
  public function isUseModelData(): bool
  {
    return $this->useModelData;
  }

  /**
   * @return string
   */
  public function getModelClassName(): string
  {
    return $this->modelClassName;
  }

  /**
   * @return string
   */
  public function getModelObjectId(): string
  {
    return $this->modelObjectId;
  }

  /**
   * @param array|null $meta
   * @param ModelAbstract|null $model
   * @throws CallUndefinedMethod
   * @throws ClassWasNotFound
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   * @throws ReflectionException
   * @throws Throwable
   */
  public function __construct(?array $meta = [], ?ModelAbstract $model = null)
  {
    foreach (array_keys(get_class_vars(self::class)) as $var) {
      if (!empty($meta[$var])) {
        if ($var === 'ogImage') {
          $this->{$var} = new File((array)$meta[$var]);
        } else {
          $this->{$var} = $meta[$var];
        }
      }
    }

    if ($model) {
      $this->modelClassName = $model::class;
      $this->modelObjectId = $model->{$model->getMeta()->getPrimary()};
    }
  }

  /**
   * @return array
   * @throws CallUndefinedMethod
   * @throws ClassWasNotFound
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   * @throws PropertyWasNotFound
   */
  public function getComputedData(): array
  {
    if ($this->isUseModelData()) {
      $objectData = $this->getObjectData();
      return [
        'title' => $objectData['title'],
        'description' => $objectData['description'],
        'ogImage' => $objectData['image'],
        'ogTitle' => $objectData['title'],
        'ogDescription' => $objectData['description'],
      ];
    }

    return [
      'title' => $this->getTitle(),
      'description' => $this->getDescription(),
      'ogImage' => $this->getOgImage(),
      'ogTitle' => $this->getOgTitle(),
      'ogDescription' => $this->getOgDescription(),
    ];
  }

  /**
   * @return string
   * @throws CallUndefinedMethod
   * @throws ClassWasNotFound
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   * @throws PropertyWasNotFound
   */
  public function __toString(): string
  {
    $data = $this->getComputedData();

    $title = $data['title'];
    $description = $data['description'];
    $ogTitle = $data['ogTitle'];
    $ogDescription = $data['ogDescription'];
    $ogImage = $data['ogImage'];

    $siteUrl = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['SERVER_NAME'];
    $canonical = $siteUrl . $_SERVER['REQUEST_URI'];
    $ogUrl = $siteUrl . explode('?', $_SERVER['REQUEST_URI'])[0];

    $ogType = 'website';

    $tags = [
      // Default Meta tags
      "<title>{$title}</title>",
      "<meta name=\"description\" content=\"{$description}\">",
      "<link rel=\"canonical\" href=\"$canonical\">",

      // OG Meta tags
      "<meta name=\"og:type\" content=\"$ogType\">",
      "<meta name=\"og:url\" content=\"$ogUrl\">",
      "<meta name=\"og:title\" content=\"{$ogTitle}\" itemprop=\"title name\">",
      "<meta name=\"og:description\" content=\"{$ogDescription}\" itemprop=\"description\">",
    ];

    if ($ogImage) {
      $tags[] = "<meta property=\"og:image\" content=\"{$ogImage->getSrc()}\" itemprop=\"image primaryImageOfPage\">";
    }

    return implode("\n", $tags);
  }

  /**
   * @return array{title: string|null, description: string|null, image: File|null}
   * @throws ClassWasNotFound
   * @throws CallUndefinedMethod
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   * @throws PropertyWasNotFound
   */
  public function getObjectData(): array
  {
    /** @var ModelAbstract $model */
    $model = $this->modelClassName;
    $object = null;

    if ($model) {
      $object = $model::fetchOne([
        (new $model)->getMeta()->getPrimary() => $this->modelObjectId
      ]);
    }

    $defaults = [
      'title' => null,
      'description' => null,
      'image' => null
    ];

    if ($object) {
      if ($object->getMeta()->hasProperty('title')
        && $object->getMeta()->getPropertyWithName('title')->getType() === 'string') {
        $defaults['title'] = mb_substr($object->title, 0, 60);

      } elseif ($object->getMeta()->hasProperty('subTitle')
        && $object->getMeta()->getPropertyWithName('subTitle')->getType() === 'string') {
        $defaults['title'] = mb_substr($object->subTitle, 0, 60);
      }

      if ($object->getMeta()->hasProperty('description')
        && $object->getMeta()->getPropertyWithName('description')->getType() === 'string') {
        $defaults['description'] = mb_substr($object->description, 0, 60);

      } elseif ($object->getMeta()->hasProperty('content')
        && $object->getMeta()->getPropertyWithName('content')->getType() === 'string') {
        $defaults['description'] = mb_substr(strip_tags($object->content), 0, 60);

      } elseif ($object->getMeta()->hasProperty('richContent')) {
        /** @var RichContent $richContent */
        foreach (($object->richContent ?? []) as $richContent) {
          if ($richContent->getType() === RichContent::TYPE_HTML) {
            $defaults['description'] = strip_tags($richContent->getType());

          } elseif ($richContent->getType() === RichContent::TYPE_TEXT) {
            $defaults['description'] = $richContent->getValue();
          }
        }
        $defaults['description'] = mb_substr($defaults['description'], 0, 60);
      }

      $defaults['image'] = null;

      if ($object->getMeta()->hasProperty('image')
        && $object->getMeta()->getPropertyWithName('image')->getType() === File::class) {
        $defaults['image'] = $object->image;

      } elseif ($object->getMeta()->hasProperty('images')
        && $object->getMeta()->getPropertyWithName('images')->getType() === File::class . '[]') {
        $defaults['image'] = $object->images[0] ?? null;
      }
    }
    return $defaults;
  }

  /**
   * @return array
   */
  public function toArray(): array
  {
    return [
      'title' => $this->title,
      'description' => $this->description,
      'ogTitle' => $this->ogTitle,
      'ogDescription' => $this->ogDescription,
      'ogImage' => $this->ogImage,
      'useModelData' => $this->useModelData,
      'modelClassName' => $this->modelClassName,
      'modelObjectId' => $this->modelObjectId
    ];
  }
}
