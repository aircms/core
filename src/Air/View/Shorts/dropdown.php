<?php

function dropdown(
  string|array         $containerClass = [],
  string|array         $containerAttributes = [],
  string|array         $containerData = [],
  string|array         $buttonClass = [],
  string|array         $boxClass = [],
  Closure|array|string $button = null,
  Closure|array|string $box = null,
): string
{
  $containerClass = (array)$containerClass;
  $containerClass[] = 'dropdown';

  $buttonClass = (array)$buttonClass;
  $buttonClass[] = 'd-f gp-5 ai-c';

  $boxClass = (array)$boxClass;
  $boxClass[] = 'dropdown-box';

  return div(
    class: $containerClass,
    attributes: $containerAttributes,
    data: $containerData,
    content: [
      a(
        class: $buttonClass,
        attributes: ['role' => 'button'],
        content: [
          ...(array)$button,
          ...[faIcon(icon: 'chevron-down', class: 'fs-10')]
        ]
      ),
      div(class: $boxClass, content: $box),
    ]
  );
}