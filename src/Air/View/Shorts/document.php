<?php

declare(strict_types=1);

function document(
  array|string|null         $htmlAttributes = null,
  array|string|null         $htmlData = null,
  array|string|null         $headAttributes = null,
  array|string|null         $headData = null,
  Closure|array|string|null $headContent = null,
  array|string|null         $bodyAttributes = null,
  array|string|null         $bodyData = null,
  array|string|null         $bodyClass = null,
  Closure|array|string|null $bodyContent = null,
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