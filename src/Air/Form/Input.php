<?php

declare(strict_types=1);

namespace Air\Form;

use Air\Crud\Locale;
use Air\Form\Element\ElementAbstract;

/**
 * @method static checkbox(string $name, mixed $value = null, string $label = null, string $description = null, string $hint = null, array $filters = null, array $validators = null, bool $allowNull = null, string $placeholder = null, string $format = null, string $phpFormat = null, array $elements = null, string $keyLabel = 'Key', string $valueLabel = 'Value', string $keyPropertyName = 'key', string $valuePropertyName = 'value', string $model = null, string $field = null, array $options = null, string $type = null, bool $multiple = null)
 * @method static date(string $name, mixed $value = null, string $label = null, string $description = null, string $hint = null, array $filters = null, array $validators = null, bool $allowNull = null, string $placeholder = null, string $format = null, string $phpFormat = null, array $elements = null, string $keyLabel = 'Key', string $valueLabel = 'Value', string $keyPropertyName = 'key', string $valuePropertyName = 'value', string $model = null, string $field = null, array $options = null, string $type = null, bool $multiple = null)
 * @method static dateTime(string $name, mixed $value = null, string $label = null, string $description = null, string $hint = null, array $filters = null, array $validators = null, bool $allowNull = null, string $placeholder = null, string $format = null, string $phpFormat = null, array $elements = null, string $keyLabel = 'Key', string $valueLabel = 'Value', string $keyPropertyName = 'key', string $valuePropertyName = 'value', string $model = null, string $field = null, array $options = null, string $type = null, bool $multiple = null)
 * @method static embed(string $name, mixed $value = null, string $label = null, string $description = null, string $hint = null, array $filters = null, array $validators = null, bool $allowNull = null, string $placeholder = null, string $format = null, string $phpFormat = null, array $elements = null, string $keyLabel = 'Key', string $valueLabel = 'Value', string $keyPropertyName = 'key', string $valuePropertyName = 'value', string $model = null, string $field = null, array $options = null, string $type = null, bool $multiple = null)
 * @method static faIcon(string $name, mixed $value = null, string $label = null, string $description = null, string $hint = null, array $filters = null, array $validators = null, bool $allowNull = null, string $placeholder = null, string $format = null, string $phpFormat = null, array $elements = null, string $keyLabel = 'Key', string $valueLabel = 'Value', string $keyPropertyName = 'key', string $valuePropertyName = 'value', string $model = null, string $field = null, array $options = null, string $type = null, bool $multiple = null)
 * @method static group(string $name, mixed $value = null, string $label = null, string $description = null, string $hint = null, array $filters = null, array $validators = null, bool $allowNull = null, string $placeholder = null, string $format = null, string $phpFormat = null, array $elements = null, string $keyLabel = 'Key', string $valueLabel = 'Value', string $keyPropertyName = 'key', string $valuePropertyName = 'value', string $model = null, string $field = null, array $options = null, string $type = null, bool $multiple = null)
 * @method static hidden(string $name, mixed $value = null, string $label = null, string $description = null, string $hint = null, array $filters = null, array $validators = null, bool $allowNull = null, string $placeholder = null, string $format = null, string $phpFormat = null, array $elements = null, string $keyLabel = 'Key', string $valueLabel = 'Value', string $keyPropertyName = 'key', string $valuePropertyName = 'value', string $model = null, string $field = null, array $options = null, string $type = null, bool $multiple = null)
 * @method static icon(string $name, mixed $value = null, string $label = null, string $description = null, string $hint = null, array $filters = null, array $validators = null, bool $allowNull = null, string $placeholder = null, string $format = null, string $phpFormat = null, array $elements = null, string $keyLabel = 'Key', string $valueLabel = 'Value', string $keyPropertyName = 'key', string $valuePropertyName = 'value', string $model = null, string $field = null, array $options = null, string $type = null, bool $multiple = null)
 * @method static keyValue(string $name, mixed $value = null, string $label = null, string $description = null, string $hint = null, array $filters = null, array $validators = null, bool $allowNull = null, string $placeholder = null, string $format = null, string $phpFormat = null, array $elements = null, string $keyLabel = 'Key', string $valueLabel = 'Value', string $keyPropertyName = 'key', string $valuePropertyName = 'value', string $model = null, string $field = null, array $options = null, string $type = null, bool $multiple = null)
 * @method static meta(string $name, mixed $value = null, string $label = null, string $description = null, string $hint = null, array $filters = null, array $validators = null, bool $allowNull = null, string $placeholder = null, string $format = null, string $phpFormat = null, array $elements = null, string $keyLabel = 'Key', string $valueLabel = 'Value', string $keyPropertyName = 'key', string $valuePropertyName = 'value', string $model = null, string $field = null, array $options = null, string $type = null, bool $multiple = null)
 * @method static model(string $name, mixed $value = null, string $label = null, string $description = null, string $hint = null, array $filters = null, array $validators = null, bool $allowNull = null, string $placeholder = null, string $format = null, string $phpFormat = null, array $elements = null, string $keyLabel = 'Key', string $valueLabel = 'Value', string $keyPropertyName = 'key', string $valuePropertyName = 'value', string $model = null, string $field = null, array $options = null, string $type = null, bool $multiple = null)
 * @method static multipleGroup(string $name, mixed $value = null, string $label = null, string $description = null, string $hint = null, array $filters = null, array $validators = null, bool $allowNull = null, string $placeholder = null, string $format = null, string $phpFormat = null, array $elements = null, string $keyLabel = 'Key', string $valueLabel = 'Value', string $keyPropertyName = 'key', string $valuePropertyName = 'value', string $model = null, string $field = null, array $options = null, string $type = null, bool $multiple = null)
 * @method static multipleKeyValue(string $name, mixed $value = null, string $label = null, string $description = null, string $hint = null, array $filters = null, array $validators = null, bool $allowNull = null, string $placeholder = null, string $format = null, string $phpFormat = null, array $elements = null, string $keyLabel = 'Key', string $valueLabel = 'Value', string $keyPropertyName = 'key', string $valuePropertyName = 'value', string $model = null, string $field = null, array $options = null, string $type = null, bool $multiple = null)
 * @method static multipleModel(string $name, mixed $value = null, string $label = null, string $description = null, string $hint = null, array $filters = null, array $validators = null, bool $allowNull = null, string $placeholder = null, string $format = null, string $phpFormat = null, array $elements = null, string $keyLabel = 'Key', string $valueLabel = 'Value', string $keyPropertyName = 'key', string $valuePropertyName = 'value', string $model = null, string $field = null, array $options = null, string $type = null, bool $multiple = null)
 * @method static multiplePage(string $name, mixed $value = null, string $label = null, string $description = null, string $hint = null, array $filters = null, array $validators = null, bool $allowNull = null, string $placeholder = null, string $format = null, string $phpFormat = null, array $elements = null, string $keyLabel = 'Key', string $valueLabel = 'Value', string $keyPropertyName = 'key', string $valuePropertyName = 'value', string $model = null, string $field = null, array $options = null, string $type = null, bool $multiple = null)
 * @method static multipleText(string $name, mixed $value = null, string $label = null, string $description = null, string $hint = null, array $filters = null, array $validators = null, bool $allowNull = null, string $placeholder = null, string $format = null, string $phpFormat = null, array $elements = null, string $keyLabel = 'Key', string $valueLabel = 'Value', string $keyPropertyName = 'key', string $valuePropertyName = 'value', string $model = null, string $field = null, array $options = null, string $type = null, bool $multiple = null)
 * @method static page(string $name, mixed $value = null, string $label = null, string $description = null, string $hint = null, array $filters = null, array $validators = null, bool $allowNull = null, string $placeholder = null, string $format = null, string $phpFormat = null, array $elements = null, string $keyLabel = 'Key', string $valueLabel = 'Value', string $keyPropertyName = 'key', string $valuePropertyName = 'value', string $model = null, string $field = null, array $options = null, string $type = null, bool $multiple = null)
 * @method static permissions(string $name, mixed $value = null, string $label = null, string $description = null, string $hint = null, array $filters = null, array $validators = null, bool $allowNull = null, string $placeholder = null, string $format = null, string $phpFormat = null, array $elements = null, string $keyLabel = 'Key', string $valueLabel = 'Value', string $keyPropertyName = 'key', string $valuePropertyName = 'value', string $model = null, string $field = null, array $options = null, string $type = null, bool $multiple = null)
 * @method static quote(string $name, mixed $value = null, string $label = null, string $description = null, string $hint = null, array $filters = null, array $validators = null, bool $allowNull = null, string $placeholder = null, string $format = null, string $phpFormat = null, array $elements = null, string $keyLabel = 'Key', string $valueLabel = 'Value', string $keyPropertyName = 'key', string $valuePropertyName = 'value', string $model = null, string $field = null, array $options = null, string $type = null, bool $multiple = null)
 * @method static richContent(string $name, mixed $value = null, string $label = null, string $description = null, string $hint = null, array $filters = null, array $validators = null, bool $allowNull = null, string $placeholder = null, string $format = null, string $phpFormat = null, array $elements = null, string $keyLabel = 'Key', string $valueLabel = 'Value', string $keyPropertyName = 'key', string $valuePropertyName = 'value', string $model = null, string $field = null, array $options = null, string $type = null, bool $multiple = null)
 * @method static select(string $name, mixed $value = null, string $label = null, string $description = null, string $hint = null, array $filters = null, array $validators = null, bool $allowNull = null, string $placeholder = null, string $format = null, string $phpFormat = null, array $elements = null, string $keyLabel = 'Key', string $valueLabel = 'Value', string $keyPropertyName = 'key', string $valuePropertyName = 'value', string $model = null, string $field = null, array $options = null, string $type = null, bool $multiple = null)
 * @method static storage(string $name, mixed $value = null, string $label = null, string $description = null, string $hint = null, array $filters = null, array $validators = null, bool $allowNull = null, string $placeholder = null, string $format = null, string $phpFormat = null, array $elements = null, string $keyLabel = 'Key', string $valueLabel = 'Value', string $keyPropertyName = 'key', string $valuePropertyName = 'value', string $model = null, string $field = null, array $options = null, string $type = null, bool $multiple = null)
 * @method static tab(string $name, mixed $value = null, string $label = null, string $description = null, string $hint = null, array $filters = null, array $validators = null, bool $allowNull = null, string $placeholder = null, string $format = null, string $phpFormat = null, array $elements = null, string $keyLabel = 'Key', string $valueLabel = 'Value', string $keyPropertyName = 'key', string $valuePropertyName = 'value', string $model = null, string $field = null, array $options = null, string $type = null, bool $multiple = null)
 * @method static text(string $name, mixed $value = null, string $label = null, string $description = null, string $hint = null, array $filters = null, array $validators = null, bool $allowNull = null, string $placeholder = null, string $format = null, string $phpFormat = null, array $elements = null, string $keyLabel = 'Key', string $valueLabel = 'Value', string $keyPropertyName = 'key', string $valuePropertyName = 'value', string $model = null, string $field = null, array $options = null, string $type = null, bool $multiple = null)
 * @method static url(string $name, mixed $value = null, string $label = null, string $description = null, string $hint = null, array $filters = null, array $validators = null, bool $allowNull = null, string $placeholder = null, string $format = null, string $phpFormat = null, array $elements = null, string $keyLabel = 'Key', string $valueLabel = 'Value', string $keyPropertyName = 'key', string $valuePropertyName = 'value', string $model = null, string $field = null, array $options = null, string $type = null, bool $multiple = null)
 * @method static textarea(string $name, mixed $value = null, string $label = null, string $description = null, string $hint = null, array $filters = null, array $validators = null, bool $allowNull = null, string $placeholder = null, string $format = null, string $phpFormat = null, array $elements = null, string $keyLabel = 'Key', string $valueLabel = 'Value', string $keyPropertyName = 'key', string $valuePropertyName = 'value', string $model = null, string $field = null, array $options = null, string $type = null, bool $multiple = null)
 * @method static tiny(string $name, mixed $value = null, string $label = null, string $description = null, string $hint = null, array $filters = null, array $validators = null, bool $allowNull = null, string $placeholder = null, string $format = null, string $phpFormat = null, array $elements = null, string $keyLabel = 'Key', string $valueLabel = 'Value', string $keyPropertyName = 'key', string $valuePropertyName = 'value', string $model = null, string $field = null, array $options = null, string $type = null, bool $multiple = null)
 * @method static treeModel(string $name, mixed $value = null, string $label = null, string $description = null, string $hint = null, array $filters = null, array $validators = null, bool $allowNull = null, string $placeholder = null, string $format = null, string $phpFormat = null, array $elements = null, string $keyLabel = 'Key', string $valueLabel = 'Value', string $keyPropertyName = 'key', string $valuePropertyName = 'value', string $model = null, string $field = null, array $options = null, string $type = null, bool $multiple = null)
 */
final class Input
{
  public static function __callStatic(string $name, array $arguments)
  {
    $inputClassName = 'Air\Form\Element\\' . ucfirst($name);
    $inputName = $arguments[0] ?? $arguments['name'];
    unset($arguments[0]);
    unset($arguments['name']);

    if (!isset($arguments['label'])) {
      $arguments['label'] = Locale::t(ElementAbstract::convertNameToLabel($inputName));
    }

    if (isset($arguments['description'])) {
      $arguments['description'] = Locale::t($arguments['description']);
    }

    return new $inputClassName($inputName, $arguments);
  }
}
