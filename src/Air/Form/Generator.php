<?php

declare(strict_types=1);

namespace Air\Form;

use Air\Filter\Lowercase;
use Air\Filter\Trim;
use Air\Form\Element\Checkbox;
use Air\Form\Element\Date;
use Air\Form\Element\DateTime;
use Air\Form\Element\ElementAbstract;
use Air\Form\Element\MultiplePage;
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
   * @param ElementAbstract[] $userElements
   * @return array
   */
  public static function defaultElement(ModelAbstract $model = null, array $userElements = []): array
  {
    $formElements = [
      'General' => [
        'enabled' => null,
        'url' => null,
        'date' => null,
        'dateTime' => null,
        'title' => null,
        'subTitle' => null,
        'description' => null,
        'quote' => null,
      ],
      'Documents' => [
        'page' => null,
        'pages' => null,
      ],
      'Images' => [
        'image' => null,
        'images' => null,
        'file' => null,
        'files' => null,
      ],
      'Content' => [
        'content' => null,
        'richContent' => null,
        'embed' => null,
      ],
      'META settings' => [
        'meta' => null,
      ],
    ];

    $count = 0;

    foreach ($userElements as $userGroupName => $userGroupElements) {
      foreach ($userGroupElements as $userGroupElement) {
        $userGroupElementName = $userGroupElement->getname();

        $formElements[$userGroupName] = $formElements[$userGroupName] ?? [];

        if (!isset($formElements[$userGroupName][$userGroupElementName])) {
          $formElements[$userGroupName][$userGroupElementName] = $userGroupElement;
        }

        foreach ($formElements as $formGroupName => $formGroupElements) {
          if ($formGroupName !== $userGroupName) {
            unset($formElements[$formGroupName][$userGroupElementName]);
          }
          $count++;
        }
      }
    }

    foreach ($formElements as $groupName => $elements) {
      foreach ($elements as $elementName => $element) {
        if ($completedElement = self::addElement($elementName, $model, $element)) {
          $formElements[$groupName][$elementName] = $completedElement;
        }
      }
      $formElements[$groupName] = array_values(array_filter($formElements[$groupName]));
    }

    return array_filter($formElements);
  }

  /**
   * @param string $name
   * @return string|null
   */
  private static function getElementClassName(string $name): ?string
  {
    switch ($name) {
      case 'url':
      case 'title':
      case 'subTitle':
      case 'subTitle':
        return Text::class;

      case 'enabled':
        return Checkbox::class;

      case 'date':
        return Date::class;

      case 'dateTime':
        return DateTime::class;

      case 'description':
        return Textarea::class;

      case 'image':
      case 'images':
      case 'file':
      case 'files':
        return Storage::class;

      case 'meta':
        return Meta::class;

      case 'quote':
        return Quote::class;

      case 'content':
        return Tiny::class;

      case 'embed':
        return Embed::class;

      case 'richContent':
        return RichContent::class;

      case 'page':
        return Page::class;

      case 'pages':
        return MultiplePage::class;

      default:
        return null;
    }
  }

  /**
   * @param string $name
   * @param ModelAbstract $model
   * @param ElementAbstract|null $userElement
   * @return ElementAbstract|null
   */
  private static function addElement(
    string          $name, ModelAbstract $model,
    ElementAbstract $userElement = null
  ): ?ElementAbstract
  {
    $hasProperty = $model->getMeta()->hasProperty($name);
    $elementClassName = self::getElementClassName($name);
    $methodExists = method_exists(self::class, $name);

    if (!$hasProperty || !$elementClassName || !$methodExists) {
      return null;
    }

    $elementOptions = call_user_func([self::class, $name]);
    $elementOptions['allowNull'] = true;

    if ($userElement) {
      $elementClassName = $userElement::class;
      $elementOptions = array_merge($elementOptions, $userElement->getUserOptions());
    }

    return new $elementClassName($name, $elementOptions);
  }

  /**
   * @return array
   */
  private static function enabled(): array
  {
    return [
      'label' => 'Enabled',
      'description' => 'If the option is disabled, the recording will not be available to users.'
    ];
  }

  /**
   * @return array
   */
  private static function url(): array
  {
    return [
      'label' => 'URL',
      'filters' => [Trim::class, Lowercase::class],
      'hint' => 'URL',
      'description' => 'Use only lower-case letters a-z and digits 0-9'
    ];
  }

  /**
   * @return array
   */
  private static function date(): array
  {
    return [
      'label' => 'Date',
    ];
  }

  /**
   * @return array
   */
  private static function dateTime(): array
  {
    return [
      'label' => 'Date/Time',
    ];
  }

  /**
   * @return array
   */
  private static function title(): array
  {
    return [
      'label' => 'Title',
      'filters' => [Trim::class],
      'allowNull' => false,
      'description' => 'Enter no more than 255 characters'
    ];
  }

  /**
   * @return array
   */
  private static function subTitle(): array
  {
    return [
      'label' => 'Sub title',
      'filters' => [Trim::class],
      'description' => 'This is a slightly expanded version of the title'
    ];
  }

  /**
   * @return array
   */
  private static function description(): array
  {
    return [
      'label' => 'Description',
      'filters' => [Trim::class],
      'description' => 'Come up with a concise description'
    ];
  }

  /**
   * @return string[]
   */
  private static function image(): array
  {
    return [
      'label' => 'Image',
    ];
  }

  /**
   * @return array
   */
  private static function images(): array
  {
    return [
      'label' => 'Images',
      'multiple' => true,
    ];
  }

  /**
   * @return string[]
   */
  private static function file(): array
  {
    return [
      'label' => 'File',
    ];
  }

  /**
   * @return array
   */
  private static function files(): array
  {
    return [
      'label' => 'Files',
      'multiple' => true,
    ];
  }

  /**
   * @return string[]
   */
  private static function meta(): array
  {
    return [
      'label' => 'Meta',
      'description' => 'Complete your web page\'s meta tags, including title, description, and keywords, to enhance ' .
        'visibility on search engines and attract your target audience.'
    ];
  }

  /**
   * @return array
   */
  private static function quote(): array
  {
    return [
      'label' => 'Quote',
      'allowNull' => true,
    ];
  }

  /**
   * @return string[]
   */
  private static function content(): array
  {
    return [
      'label' => 'Content',
      'description' => 'Input and edit text with diverse styles, sizes, along with intuitive formatting options such' .
        ' as bold, italic, and underline, as well as support for various data types like links.'
    ];
  }

  /**
   * @return string[]
   */
  private static function embed(): array
  {
    return [
      'label' => 'Embed',
      'description' => 'YouTube, Vimeo, Google Maps or other embed url'
    ];
  }

  /**
   * @return string[]
   */
  private static function richContent(): array
  {
    return [
      'label' => 'Rich content',
      'description' => 'Input and edit a different content types, such as Html, Text, Images, Quote and Code snippets.'
    ];
  }

  /**
   * @return array
   */
  private static function page(): array
  {
    return [
      'label' => 'Document',
      'description' => 'Pdf driven similar block.<br>You can resize the canvas and add elements such as:<br><ul><li>' .
        'Image</li><li>Video</li><li>Document</li><li>Rich text</li><li>Embed</li></ul>'
    ];
  }

  /**
   * @return array
   */
  private static function pages(): array
  {
    return [...self::page(), ...[
      'label' => 'Documents',
    ]];
  }
}
