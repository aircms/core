<?php

declare(strict_types=1);

namespace Air\Form;

use Air\Core\Front;
use Air\Crud\Locale;
use Air\Crud\Model\Language;
use Air\Crud\Nav;
use Air\Filter\Lowercase;
use Air\Filter\Trim;
use Air\Form\Element\Checkbox;
use Air\Form\Element\Date;
use Air\Form\Element\DateTime;
use Air\Form\Element\ElementAbstract;
use Air\Form\Element\Embed;
use Air\Form\Element\FaIcon;
use Air\Form\Element\Icon;
use Air\Form\Element\Meta;
use Air\Form\Element\Model;
use Air\Form\Element\MultipleModel;
use Air\Form\Element\MultiplePage;
use Air\Form\Element\Page;
use Air\Form\Element\Quote;
use Air\Form\Element\RichContent;
use Air\Form\Element\Select;
use Air\Form\Element\Storage;
use Air\Form\Element\Text;
use Air\Form\Element\Textarea;
use Air\Form\Element\Tiny;
use Air\Form\Element\Url;
use Air\Model\ModelAbstract;

final class Generator
{
  public static function minimal(ModelAbstract $model = null, array $elements = []): Form
  {
    return new Form(['data' => $model], self::defaultElement($model, $elements));
  }

  public static function full(ModelAbstract $model = null, array $elements = []): Form
  {
    return new Form(['data' => $model], self::defaultElement($model, $elements, true));
  }

  public static function fullRequired(ModelAbstract $model, array $elements = []): Form
  {
    return new Form(['data' => $model], self::defaultElement($model, $elements, true, false));
  }

  public static function defaultElement(
    ModelAbstract $model = null,
    array         $userElements = [],
    ?bool         $includeReferences = false,
    bool          $allowNull = true
  ): array
  {
    if (count($userElements) && isset($userElements[0])) {
      $userElements = [
        'General' => $userElements
      ];
    }

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
        'faIcon' => null,
        'image' => null,
        'images' => null,
        'file' => null,
        'files' => null,
        'name' => null,
        'login' => null,
        'password' => null,
        'language' => null
      ],
      'Documents' => [
        'page' => null,
        'pages' => null,
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

    if (!Nav::getSettingsItem(Nav::SETTINGS_LANGUAGES)) {
      $formElements['General']['language'] = null;
    }

    foreach ($model->getMeta()->getProperties() as $property) {
      if ($property->isEnum()) {
        $formElements['General'][$property->getName()] = null;
      }
    }

    if ($includeReferences) {
      $formElements['References'] = [];

      foreach ($model->getMeta()->getProperties() as $property) {
        if ($property->getName() === 'language' && $property->getType() === Language::class) {
          continue;
        }
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

  private static function getElementClassName(string $name, ModelAbstract $model): ?string
  {
    if ($model->getMeta()->hasProperty($name) && $model->getMeta()->getPropertyWithName($name)->isEnum()) {
      return Select::class;
    }

    return match ($name) {
      'language' => Model::class,
      'url' => Url::class,
      'title', 'subTitle', 'name', 'login', 'password' => Text::class,
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
      'faIcon' => FaIcon::class,
      default => null,
    };
  }

  private static function addElement(
    string          $name,
    ModelAbstract   $model,
    ElementAbstract $userElement = null,
  ): ?ElementAbstract
  {
    $hasProperty = $model->getMeta()->hasProperty($name);
    $elementClassName = self::getElementClassName($name, $model);

    if (!$hasProperty || !$elementClassName) {
      return null;
    }

    if (method_exists(self::class, $name)) {
      $elementOptions = call_user_func([self::class, $name]);
    } else {
      $elementOptions = self::other($name, $model);
    }

    if ($userElement) {
      $elementClassName = $userElement::class;
      $elementOptions = array_merge($elementOptions, $userElement->getUserOptions());
    }

    return new $elementClassName($name, $elementOptions);
  }

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
      'field' => 'title',
      'allowNull' => $allowNull
    ];

    if (is_callable([self::class, $name])) {
      $elementOptions = array_merge($elementOptions, call_user_func([self::class, $name]));
    }

    if ($userElement) {
      $elementClassName = $userElement::class;
      $elementOptions = array_merge($elementOptions, $userElement->getUserOptions());
    }

    return new $elementClassName($name, $elementOptions);
  }

  private static function language(): array
  {
    return [
      'label' => Locale::t('Language'),
      'allowNull' => false
    ];
  }

  private static function url(): array
  {
    return [
      'label' => 'URL',
      'filters' => [Trim::class, Lowercase::class],
      'allowNull' => false,
      'hint' => 'URL',
      'description' => Locale::t('Use only lower-case letters a-z and digits 0-9')
    ];
  }

  private static function enabled(): array
  {
    return [
      'label' => Locale::t('Enabled'),
      'description' => Locale::t('If the option is disabled, the recording will not be available to users')
    ];
  }

  private static function date(): array
  {
    return [
      'label' => Locale::t('Date'),
    ];
  }

  private static function dateTime(): array
  {
    return [
      'label' => Locale::t('Date/Time'),
    ];
  }

  private static function title(): array
  {
    return [
      'label' => Locale::t('Title'),
      'filters' => [Trim::class],
      'allowNull' => false,
      'description' => Locale::t('Enter no more than 255 characters')
    ];
  }

  private static function subTitle(): array
  {
    return [
      'label' => Locale::t('Sub title'),
      'filters' => [Trim::class],
      'allowNull' => false,
      'description' => Locale::t('This is a slightly expanded version of the title')
    ];
  }

  private static function description(): array
  {
    return [
      'label' => Locale::t('Description'),
      'filters' => [Trim::class],
      'allowNull' => false,
      'description' => Locale::t('Come up with a concise description')
    ];
  }

  private static function image(): array
  {
    return [
      'label' => Locale::t('Image'),
      'allowNull' => false,
    ];
  }

  private static function images(): array
  {
    return [
      'label' => Locale::t('Images'),
      'multiple' => true,
      'allowNull' => false,
    ];
  }

  private static function file(): array
  {
    return [
      'label' => Locale::t('File'),
      'allowNull' => false,
    ];
  }

  private static function files(): array
  {
    return [
      'label' => Locale::t('Files'),
      'multiple' => true,
      'allowNull' => false,
    ];
  }

  private static function meta(): array
  {
    return [
      'label' => Locale::t('Meta'),
      'description' => Locale::t('Complete your web page meta tags, including title, description, and keywords, to enhance ' .
        'visibility on search engines and attract your target audience.'),
      'allowNull' => false,
    ];
  }

  private static function quote(): array
  {
    return [
      'label' => Locale::t('Quote'),
      'allowNull' => true,
    ];
  }

  private static function content(): array
  {
    return [
      'label' => Locale::t('Content'),
      'description' => Locale::t('Input and edit text with diverse styles, sizes, along with intuitive formatting options such' .
        ' as bold, italic, and underline, as well as support for various data types like links.'),
      'allowNull' => false,
    ];
  }

  private static function embed(): array
  {
    return [
      'label' => Locale::t('Embed'),
      'description' => Locale::t('YouTube, Vimeo, Google Maps or other embed url')
    ];
  }

  private static function richContent(): array
  {
    return [
      'label' => Locale::t('Rich content'),
      'description' => Locale::t('Input and edit a different content types, such as Html, Text, Images, Quote and Code snippets.'),
      'allowNull' => false,
    ];
  }

  private static function page(): array
  {
    return [
      'label' => Locale::t('Document'),
      'description' => Locale::t('Pdf driven similar block.<br>You can resize the canvas and add elements such as:<br><ul><li>' .
        'Image</li><li>Video</li><li>Document</li><li>Rich text</li><li>Embed</li></ul>')
    ];
  }

  private static function pages(): array
  {
    return [...self::page(), ...[
      'label' => Locale::t('Documents'),
    ]];
  }

  private static function icon(): array
  {
    return [
      'label' => Locale::t('Icon (Google Symbol)'),
      'description' => Locale::t('Use icons name from Google Symbols<br><a href="https://fonts.google.com/icons"' .
        ' class="text-info text-decoration-underline" target="_blank">Google Symbols</a>'),
      'allowNull' => false,
    ];
  }

  private static function faIcon(): array
  {
    return [
      'label' => Locale::t('Font Awesome v6.6'),
      'description' => Locale::t('Use icons name from Font Awesome<br><a href="https://fontawesome.com/search"' .
        ' class="text-info text-decoration-underline" target="_blank">Font Awesome</a>'),
      'allowNull' => false,
    ];
  }

  private static function other(string $name, ModelAbstract $model): array
  {
    $title = preg_replace('/([a-z])([A-Z])/', '$1 $2', $name);
    return [
      'label' => Locale::t(ucfirst(strtolower($title))),
      'description' => Locale::t('Select a value from the enumeration'),
      'options' => $model->getMeta()->getPropertyWithName($name)->getEnum(),
      'allowNull' => false,
    ];
  }

  private static function name(): array
  {
    return [
      'label' => Locale::t('Name'),
      'allowNull' => false,
    ];
  }

  private static function login(): array
  {
    return [
      'label' => Locale::t('Login'),
      'allowNull' => false,
    ];
  }

  private static function password(): array
  {
    return [
      'label' => Locale::t('Password'),
      'allowNull' => false,
    ];
  }
}
