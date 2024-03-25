<?php

declare(strict_types=1);

namespace Air\Form;

use Air\Filter\Lowercase;
use Air\Filter\Trim;
use Air\Form\Element\Checkbox;
use Air\Form\Element\Date;
use Air\Form\Element\DateTime;
use Air\Form\Element\Page;
use Air\Form\Element\Embed;
use Air\Form\Element\Meta;
use Air\Form\Element\MultipleModel;
use Air\Form\Element\Quote;
use Air\Form\Element\RichContent;
use Air\Form\Element\Model;
use Air\Form\Element\Storage;
use Air\Form\Element\Text;
use Air\Form\Element\Textarea;
use Air\Form\Element\Tiny;
use Air\Model\ModelAbstract;

final class Generator
{
  /**
   * @var ModelAbstract[]|ModelAbstract
   */
  public mixed $data;

  /**
   * @param ModelAbstract|null $model
   * @param array $elements
   * @return Form
   */
  public static function default(ModelAbstract $model = null, array $elements = []): Form
  {
    return new Form(['data' => $model], self::defaultElement($model, $elements));
  }

  /**
   * @param ModelAbstract|null $model
   * @param array $elements
   * @return Form
   */
  public static function full(ModelAbstract $model = null, array $elements = []): Form
  {
    return new Form(['data' => $model], self::defaultElement($model, self::modelInputs($model, $elements)));
  }

  /**
   * @param ModelAbstract|null $model
   * @param array $elements
   * @return array
   */
  public static function modelInputs(ModelAbstract $model = null, array $elements = []): array
  {
    $modelElements = ['References' => []];

    foreach ($model->getMeta()->getProperties() as $property) {
      $type = $property->getType();

      if (class_exists($type) && is_subclass_of($type, ModelAbstract::class)) {

        $modelName = explode('\\', $type);
        $modelName = ucfirst(trim(strtolower(implode(' ', preg_split('/(?=[A-Z])/', end($modelName))))));
        $fieldName = ucfirst(strtolower(implode(' ', preg_split('/(?=[A-Z])/', $property->getName()))));

        $modelElements['References'][] = new Model($property->getName(), [
          'value' => $model->{$property->getName()},
          'label' => $fieldName,
          'model' => $type,
          'description' => 'Please select an entry from the ' . $modelName . ' collection',
          'field' => 'title',
          'allowNull' => true
        ]);

      } elseif (str_ends_with($type, '[]')) {
        $type = substr($type, 0, strlen($type) - 2);

        $modelName = explode('\\', $type);
        $modelName = ucfirst(trim(strtolower(implode(' ', preg_split('/(?=[A-Z])/', end($modelName))))));
        $fieldName = ucfirst(strtolower(implode(' ', preg_split('/(?=[A-Z])/', $property->getName()))));

        if (class_exists($type) && is_subclass_of($type, ModelAbstract::class)) {
          $modelElements['References'][] = new MultipleModel($property->getName(), [
            'value' => $model->{$property->getName()},
            'label' => $fieldName,
            'model' => $type,
            'description' => 'Please select an entries from the ' . $modelName . ' collection',
            'field' => 'title',
            'allowNull' => true
          ]);
        }
      }
    }
    return array_merge_recursive(array_filter($modelElements), $elements);
  }

  /**
   * @param ModelAbstract|null $model
   * @param array $elements
   * @return array
   */
  public static function defaultElement(ModelAbstract $model = null, array $elements = []): array
  {
    return array_merge_recursive(
      array_filter([
        'Documents' => array_filter([
          self::document($model),
          self::documents($model),
        ]),
        'General' => array_filter([
          self::quote($model),
          self::enabled($model),
          self::url($model),
          self::date($model),
          self::dateTime($model),
          self::title($model),
          self::subTitle($model),
          self::description($model),
        ]),
        'Images' => array_filter([
          self::image($model),
          self::images($model),
          self::file($model),
          self::files($model),
        ]),
        'Content' => array_filter([
          self::content($model),
          self::richContent($model),
          self::embed($model),
        ]),
        'Socials' => array_filter(
          self::socials($model)
        ),
        'META settings' => array_filter([
          self::meta($model)
        ]),
      ]),
      $elements
    );
  }

  /**
   * @param ModelAbstract $model
   * @param string $property
   * @return bool
   */
  public static function hasProperty(ModelAbstract $model, string $property): bool
  {
    return $model->getMeta()->hasProperty($property);
  }


  /**
   * @param ModelAbstract $model
   * @return Text|null
   */
  public static function url(ModelAbstract $model): ?Text
  {
    return !self::hasProperty($model, 'url') ? null :
      new Text('url', [
        'value' => $model->url,
        'label' => 'URL',
        'filters' => [Trim::class, Lowercase::class],
        'hint' => 'URL',
        'description' => 'Use only lower-case letters a-z and digits 0-9'
      ]);
  }

  /**
   * @param ModelAbstract $model
   * @return Checkbox|null
   */
  public static function enabled(ModelAbstract $model): ?Checkbox
  {
    return !self::hasProperty($model, 'enabled') ? null :
      new Checkbox('enabled', [
        'value' => (!strlen($model->id)) ? true : $model->enabled,
        'label' => 'Enabled',
        'description' => 'If the option is disabled, the recording will not be available to users.'
      ]);
  }

  /**
   * @param ModelAbstract $model
   * @return Date|null
   */
  public static function date(ModelAbstract $model): ?Date
  {
    return !self::hasProperty($model, 'date') ? null :
      new Date('date', [
        'value' => $model->date,
        'label' => 'Date',
        'allowNull' => true
      ]);
  }

  /**
   * @param ModelAbstract $model
   * @return DateTime|null
   */
  public static function dateTime(ModelAbstract $model): ?DateTime
  {
    return !self::hasProperty($model, 'dateTime') ? null :
      new DateTime('dateTime', [
        'value' => $model->dateTime,
        'label' => 'Date/Time',
        'allowNull' => true
      ]);
  }

  /**
   * @param ModelAbstract $model
   * @return Text|null
   */
  public static function title(ModelAbstract $model): ?Text
  {
    return !self::hasProperty($model, 'title') ? null :
      new Text('title', [
        'value' => $model->title,
        'label' => 'Title',
        'filters' => [Trim::class],
        'allowNull' => false,
        'description' => 'Enter no more than 255 characters'
      ]);
  }

  /**
   * @param ModelAbstract $model
   * @return Text|null
   */
  public static function subTitle(ModelAbstract $model): ?Text
  {
    return !self::hasProperty($model, 'subTitle') ? null :
      new Text('subTitle', [
        'value' => $model->subTitle,
        'label' => 'Sub title',
        'filters' => [Trim::class],
        'allowNull' => true,
        'description' => 'This is a slightly expanded version of the title'
      ]);
  }

  /**
   * @param ModelAbstract $model
   * @return Textarea|null
   */
  public static function description(ModelAbstract $model): ?Textarea
  {
    return !self::hasProperty($model, 'description') ? null :
      new Textarea('description', [
        'value' => $model->description,
        'label' => 'Description',
        'filters' => [Trim::class],
        'description' => 'Come up with a concise description'
      ]);
  }

  /**
   * @param ModelAbstract $model
   * @return Storage|null
   */
  public static function image(ModelAbstract $model): ?Storage
  {
    return !self::hasProperty($model, 'image') ? null :
      new Storage('image', [
        'value' => $model->image,
        'label' => 'Image',
        'allowNull' => true
      ]);
  }

  /**
   * @param ModelAbstract $model
   * @return Storage|null
   */
  public static function images(ModelAbstract $model): ?Storage
  {
    return !self::hasProperty($model, 'images') ? null :
      new Storage('images', [
        'value' => $model->images,
        'label' => 'Images',
        'allowNull' => true,
        'multiple' => true,
      ]);
  }

  /**
   * @param ModelAbstract $model
   * @return Storage|null
   */
  public static function file(ModelAbstract $model): ?Storage
  {
    return !self::hasProperty($model, 'file') ? null :
      new Storage('file', [
        'value' => $model->file,
        'label' => 'File',
        'allowNull' => true
      ]);
  }

  /**
   * @param ModelAbstract $model
   * @return Storage|null
   */
  public static function files(ModelAbstract $model): ?Storage
  {
    return !self::hasProperty($model, 'files') ? null :
      new Storage('files', [
        'value' => $model->files,
        'label' => 'Files',
        'allowNull' => true,
        'multiple' => true,
      ]);
  }

  /**
   * @param ModelAbstract $model
   * @return Meta|null
   */
  public static function meta(ModelAbstract $model): ?Meta
  {
    return !self::hasProperty($model, 'meta') ? null :
      new Meta('meta', [
        'value' => $model->meta,
        'label' => 'Meta',
        'allowNull' => true,
        'description' => 'Complete your web page\'s meta tags, including title, description, and keywords, to enhance visibility on search engines and attract your target audience.'
      ]);
  }

  /**
   * @param ModelAbstract $model
   * @return Quote|null
   */
  public static function quote(ModelAbstract $model): ?Quote
  {
    return !self::hasProperty($model, 'quote') ? null :
      new Quote('quote', [
        'value' => $model->quote,
        'label' => 'Quote',
        'allowNull' => true,
      ]);
  }

  /**
   * @param ModelAbstract $model
   * @return Tiny|null
   */
  public static function content(ModelAbstract $model): ?Tiny
  {
    return !self::hasProperty($model, 'content') ? null :
      new Tiny('content', [
        'value' => $model->content,
        'label' => 'Content',
        'allowNull' => true,
        'description' => 'Input and edit text with diverse styles, sizes, along with intuitive formatting options such as bold, italic, and underline, as well as support for various data types like links.'
      ]);
  }

  /**
   * @param ModelAbstract $model
   * @return Embed|null
   */
  public static function embed(ModelAbstract $model): ?Embed
  {
    return !self::hasProperty($model, 'embed') ? null :
      new Embed('embed', [
        'value' => $model->embed,
        'label' => 'Embed',
        'allowNull' => true,
        'description' => 'YouTube, Vimeo, Google Maps or other embed url'
      ]);
  }

  /**
   * @param ModelAbstract $model
   * @return RichContent|null
   */
  public static function richContent(ModelAbstract $model): ?RichContent
  {
    return !self::hasProperty($model, 'richContent') ? null :
      new RichContent('richContent', [
        'value' => $model->richContent,
        'label' => 'Rich content',
        'allowNull' => true,
        'description' => 'Input and edit a different content types, such as Html, Text, Images, Quote and Code snippets.'
      ]);
  }

  /**
   * @param ModelAbstract $model
   * @return array|null
   */
  public static function socials(ModelAbstract $model): ?array
  {
    $socials = [
      'facebook',
      'youtube',
      'instagram',
      'twitter',
      'linkedin'
    ];

    $elements = [];

    foreach ($socials as $social) {
      $propertyName = 'social' . ucfirst($social);

      if (self::hasProperty($model, $propertyName)) {
        $elements[] = new Text($propertyName, [
          'value' => $model->{$propertyName},
          'label' => ucfirst($social),
          'description' => 'Provide a link to your ' . ucfirst($social) . ' page',
          'allowNull' => true
        ]);
      }
    }

    return $elements;
  }

  /**
   * @param ModelAbstract $model
   * @return array|null
   */
  public static function document(ModelAbstract $model): ?Page
  {
    return !self::hasProperty($model, 'page') ? null :
      new Page('page', [
        'value' => $model->page,
        'label' => 'Document',
        'allowNull' => true,
        'isMultiple' => false,
        'description' => 'Pdf driven similar block.<br>You can resize the canvas and add elements such as:<br><ul><li>Image</li><li>Video</li><li>Document</li><li>Rich text</li><li>Embed</li></ul>'
      ]);
  }

  /**
   * @param ModelAbstract $model
   * @return array|null
   */
  public static function documents(ModelAbstract $model): ?Page
  {
    return !self::hasProperty($model, 'pages') ? null :
      new Page('pages', [
        'value' => $model->pages,
        'label' => 'Documents',
        'allowNull' => true,
        'isMultiple' => true,
        'description' => 'Pdf driven similar block.<br>You can resize the canvas and add elements such as:<br><ul><li>Image</li><li>Video</li><li>Document</li><li>Rich text</li><li>Embed</li></ul>Click "+ Add Page" to start'
      ]);
  }
}
