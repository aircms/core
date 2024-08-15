<?php

declare(strict_types=1);

/**
 * @param string|null $title
 * @param Closure|array|string|null $content
 * @param bool $opened
 * @param string|null $mainClass
 * @param string|null $titleClass
 * @param string|null $bodyClass
 * @return string
 */
function document(
  array|string         $htmlAttributes = null,
  array|string         $htmlData = null,
  array|string         $headAttributes = null,
  array|string         $headData = null,
  Closure|array|string $headContent = null,
  array|string         $bodyAttributes = null,
  array|string         $bodyData = null,
  array|string         $bodyClass = null,
  Closure|array|string $bodyContent = null,
): string
{
  return
    doctype() .
    tag(
      tagName: 'html',
      attributes: $htmlAttributes,
      data: $htmlData,
      content: function () use ($headAttributes, $headData, $headContent, $bodyAttributes, $bodyData, $bodyContent, $bodyClass) {
        echo tag(
          tagName: 'head',
          attributes: $headAttributes,
          data: $headData,
          content: $headContent
        );
        echo tag(
          tagName: 'body',
          attributes: $bodyAttributes,
          data: $bodyData,
          class: $bodyClass,
          content: $bodyContent
        );
      },
    );
}