<?php

declare(strict_types=1);

function hidden(
  string $name = null,
  string $value = null,
  array  $attributes = [],
): string
{
  return tag(
    tagName: 'input',
    attributes: [
      ...$attributes,
      ...[
        'name' => $name,
        'value' => $value,
        'type' => 'hidden',
      ]
    ]
  );
}

function text(
  string       $name = null,
  string       $value = null,
  array|string $class = null,
  array        $attributes = [],
  string       $placeholder = null,
): string
{
  return tag(
    tagName: 'input',
    class: $class,
    attributes: [
      ...$attributes,
      ...[
        'value' => $value,
        'name' => $name,
        'placeholder' => $placeholder
      ]
    ]
  );
}

function tel(
  string       $name = null,
  string       $value = null,
  array|string $class = null,
  array        $attributes = [],
  string       $placeholder = null,
): string
{
  return tag(
    tagName: 'input',
    class: $class,
    attributes: [
      ...$attributes,
      ...[
        'value' => $value,
        'name' => $name,
        'type' => 'tel',
        'placeholder' => $placeholder
      ]
    ]
  );
}

function number(
  string       $name = null,
  string       $value = null,
  array|string $class = null,
  array        $attributes = [],
  string       $placeholder = null,
): string
{
  return tag(
    tagName: 'input',
    class: $class,
    attributes: [
      ...$attributes,
      'value' => $value,
      'name' => $name,
      'type' => 'number',
      'placeholder' => $placeholder
    ]
  );
}

function email(
  string       $name = null,
  string       $value = null,
  array|string $class = null,
  array        $attributes = [],
  string       $placeholder = null,
): string
{
  return tag(
    tagName: 'input',
    class: $class,
    attributes: [
      ...$attributes,
      ...[
        'value' => $value,
        'name' => $name,
        'type' => 'email',
        'placeholder' => $placeholder
      ]
    ]
  );
}

function select(
  string       $name = null,
  mixed        $value = null,
  array        $options = null,
  array|string $class = null,
  array        $attributes = [],
): string
{
  $opts = [];
  $assoc = !!empty($options[0]);

  foreach ($options as $optionValue => $optionTitle) {
    if ($assoc) {
      $selected = $value === $optionValue;
    } else {
      $selected = $value === $optionTitle;
    }

    $opts[] = tag(
      tagName: 'option',
      attributes: $selected ? ['selected' => 'selected'] : null,
      content: $optionTitle
    );
  }

  return div(
    class: 'select',
    content: tag(
      tagName: 'select',
      content: $opts,
      class: $class,
      attributes: [
        ...$attributes,
        ...['name' => $name,]
      ],
    )
  );
}

function label(
  string|array                        $class = null,
  Closure|string|array|null|Generator $content = null,
  array                               $attributes = [],
  string                              $for = null,
  bool                                $required = false
): string
{
  $content = [$content];

  if ($required) {
    $content[] = span(class: 'c-danger', content: '*');
  }

  return tag(
    class: $class,
    tagName: 'label',
    content: content($content),
    attributes: [
      ...$attributes,
      ...['for' => $for]
    ],
  );
}