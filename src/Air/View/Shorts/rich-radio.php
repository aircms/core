<?php

declare(strict_types=1);

function richRadio(
  string  $name = '',
  ?string $value = null,
  array   $radios = [],
  ?string $mainClass = null,
  ?string $titleClass = null,
  ?string $bodyClass = null
): string
{
  $containers = [
    hidden($name, $value, ['data-rich-radio-value'])
  ];

  $radios = array_filter($radios);

  /**
   * @var string $title
   * @var Closure|array|string|null $content
   */
  foreach ($radios as $content) {

    $containers[] = div(
      class: ['br-4 an-2 bw-2 bc-level-1-bg bc-level-3-bg-hover an-2', $content['value'] === $value || count($radios) == 1 ? 'sc-level-1' : null],
      attributes: ['data-rich-radio-container'],
      content: [
        div(
          class: ['p-16', 'px-18', 'cp', 'fw-5', 'd-f', 'ai-c', 'gp-5', 'title', $titleClass],
          attributes: [
            'data-rich-radio-button' => $content['value'],
            $content['value'] === $value || count($radios) == 1 ? 'data-rich-radio-active' : null
          ],
          content: [
            faIcon('circle', class: 'c-primary', attributes: ['data-rich-radio-icon']),
            div(content: content($content['title']))
          ]
        ),
        !empty($content['content']) ? div(
          class: 'pt-10 body',
          attributes: ['data-rich-radio-content'],
          content: div(
            class: ['p-16 pt-0', $bodyClass],
            content: content($content['content'])
          )
        ) : null
      ]
    );
  }

  return div(
    class: ['d-f', 'f-c', 'gp-10', 'rich-radio', $mainClass],
    attributes: ['data-rich-radio'],
    content: $containers
  );
}