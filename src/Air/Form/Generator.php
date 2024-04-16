<?php

declare(strict_types=1);

namespace Air\Form;

use Air\Filter\Lowercase;
use Air\Filter\Trim;
use Air\Form\Element\Checkbox;
use Air\Form\Element\Date;
use Air\Form\Element\DateTime;
use Air\Form\Element\ElementAbstract;
use Air\Form\Element\Icon;
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
use Air\Model\Meta\Exception\PropertyWasNotFound;
use Air\Model\ModelAbstract;

final class Generator
{
  /**
   * @param ModelAbstract|null $model
   * @param array $elements
   * @return Form
   * @throws PropertyWasNotFound
   */
  public static function minimal(ModelAbstract $model = null, array $elements = []): Form
  {
    return new Form(['data' => $model], self::defaultElement($model, $elements));
  }

  /**
   * @param ModelAbstract|null $model
   * @param array $elements
   * @return Form
   * @throws PropertyWasNotFound
   */
  public static function full(ModelAbstract $model = null, array $elements = []): Form
  {
    return new Form(['data' => $model], self::defaultElement($model, $elements, true));
  }

  /**
   * @param ModelAbstract $model
   * @param array $elements
   * @return Form
   * @throws PropertyWasNotFound
   */
  public static function fullRequired(ModelAbstract $model, array $elements = []): Form
  {
    return new Form(['data' => $model], self::defaultElement($model, $elements, true, false));
  }

  /**
   * @param ModelAbstract|null $model
   * @param array $userElements
   * @param bool|null $includeReferences
   * @param bool $allowNull
   * @return array
   * @throws PropertyWasNotFound
   */
  public static function defaultElement(
    ModelAbstract $model = null,
    array         $userElements = [],
    ?bool         $includeReferences = false,
    bool          $allowNull = true
  ): array
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
        'icon' => null,
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

    if ($includeReferences) {
      $formElements['References'] = [];

      foreach ($model->getMeta()->getProperties() as $property) {
        $type = $property->getType();
        if (str_ends_with($type, '[]')) {
          $type = substr($type, 0, strlen($type) - 2);
        }
        if (class_exists($type) && is_subclass_of($type, ModelAbstract::class)) {
          $formElements['References'][$property->getName()] = null;
        }
      }
    }

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
        }
      }
    }

    foreach ($formElements as $groupName => $elements) {
      foreach ($elements as $elementName => $element) {

        if ($model->getMeta()->hasProperty($elementName)) {
          $property = $model->getMeta()->getPropertyWithName($elementName);
          $type = $property->getType();
          if (str_contains($type, '[]')) {
            $type = substr($type, 0, strlen($type) - 2);
          }

          if (is_subclass_of($type, ModelAbstract::class)
            && $completedElement = self::addModelElement($elementName, $model, $element, $allowNull)
          ) {
            $formElements[$groupName][$elementName] = $completedElement;

          } elseif ($completedElement = self::addElement($elementName, $model, $element, $allowNull)) {
            $formElements[$groupName][$elementName] = $completedElement;
          }
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
    return match ($name) {
      'url', 'title', 'subTitle' => Text::class,
      'enabled' => Checkbox::class,
      'date' => Date::class,
      'dateTime' => DateTime::class,
      'description' => Textarea::class,
      'image', 'images', 'file', 'files' => Storage::class,
      'meta' => Meta::class,
      'quote' => Quote::class,
      'content' => Tiny::class,
      'embed' => Embed::class,
      'richContent' => RichContent::class,
      'page' => Page::class,
      'pages' => MultiplePage::class,
      'icon' => Icon::class,
      default => null,
    };
  }

  /**
   * @param string $name
   * @param ModelAbstract $model
   * @param ElementAbstract|null $userElement
   * @param bool $allowNull
   * @return ElementAbstract|null
   */
  private static function addElement(
    string          $name,
    ModelAbstract   $model,
    ElementAbstract $userElement = null,
    bool            $allowNull = true
  ): ?ElementAbstract
  {
    $hasProperty = $model->getMeta()->hasProperty($name);
    $elementClassName = self::getElementClassName($name);
    $methodExists = method_exists(self::class, $name);

    if (!$hasProperty || !$elementClassName || !$methodExists) {
      return null;
    }

    $elementOptions = call_user_func([self::class, $name]);
    $elementOptions['allowNull'] = $allowNull;

    if ($userElement) {
      $elementClassName = $userElement::class;
      $elementOptions = array_merge($elementOptions, $userElement->getUserOptions());
    }

    return new $elementClassName($name, $elementOptions);
  }

  /**
   * @param string $name
   * @param ModelAbstract $model
   * @param ElementAbstract|null $userElement
   * @param bool $allowNull
   * @return ElementAbstract|null
   * @throws PropertyWasNotFound
   */
  private static function addModelElement(
    string           $name,
    ModelAbstract    $model,
    ?ElementAbstract $userElement = null,
    bool             $allowNull = true
  ): ?ElementAbstract
  {
    $property = $model->getMeta()->getPropertyWithName($name);
    $type = $property->getType();
    $elementClassName = Model::class;

    if (str_contains($type, '[]')) {
      $type = substr($type, 0, strlen($type) - 2);
      $elementClassName = MultipleModel::class;
    }

    $modelName = explode('\\', $type);
    $modelName = ucfirst(trim(strtolower(implode(' ', preg_split('/(?=[A-Z])/', end($modelName))))));
    $fieldName = ucfirst(strtolower(implode(' ', preg_split('/(?=[A-Z])/', $property->getName()))));

    $elementOptions = [
      'value' => $model->{$name},
      'label' => $fieldName,
      'model' => $type,
      'description' => 'Please select an entry from the ' . $modelName . ' collection',
      'field' => 'title',
      'allowNull' => $allowNull
    ];

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

  /**
   * @return string[]
   */
  private static function icon(): array
  {
    return [
      'label' => 'Icon (Google Symbol)',
      'description' => 'Use icons name from Google Symbols<br><a href="https://fonts.google.com/icons"' .
        ' class="text-info text-decoration-underline" target="_blank">Google Symbols</a>'
    ];
  }
}
