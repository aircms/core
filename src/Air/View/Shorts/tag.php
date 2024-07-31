<?php

declare(strict_types=1);

use Air\Core\Exception\ClassWasNotFound;
use Air\Type\File;

/**
 * @param string $tagName
 * @return bool
 */
function isNonClosingTag(string $tagName): bool
{
  return in_array(trim(strtolower($tagName)), [
    'meta',
    'base',
    'br',
    'hr',
    'link'
  ]);
}

/**
 * @param Closure|string|array|null $content
 * @return array
 */
function content(Closure|string|array|null $content = null): array
{
  if ($content) {

    if (is_string($content)) {
      $content = (array)$content;

    } else if ($content instanceof Closure) {
      ob_start();
      $content = $content();
      if (!$content) {
        $content = ob_get_contents();
      }
      if ($content instanceof Generator || is_array($content)) {
        $contentItems = [];
        foreach ($content as $contentItem) {
          $contentItems[] = $contentItem;
        }
        $content = $contentItems;
      }
      ob_end_clean();
    }

    return (array)$content;
  }

  return [];
}

/**
 * @param string $tagName
 * @param Closure|string|array|null $content
 * @param string|array|null $class
 * @param array $attributes
 * @param File|string|null $bgImage
 * @return string
 * @throws ClassWasNotFound
 */
function tag(
  string                    $tagName,
  Closure|string|array|null $content = null,
  string|array              $class = null,
  array                     $attributes = [],
  File|string               $bgImage = null
): string
{
  $tagName = trim(strtolower($tagName));

  if (!is_array($class)) {
    $class = (array)$class;
  }

  if (count($class)) {
    $class = 'class="' . implode(' ', $class) . '"';
  }

  foreach ($attributes as $key => $value) {
    if (!is_int($key)) {
      $attributes[] = $key . '="' . $value . '"';
      unset($attributes[$key]);
    } else {
      $attributes[] = $value;
    }
  }

  if ($bgImage) {
    if ($bgImage instanceof File) {
      $bgImage['data-bg'] = $bgImage->getSrc();

    } elseif (str_starts_with('http:/', $bgImage) || str_starts_with('https:/', $bgImage)) {
      $bgImage['data-bg'] = $bgImage;

    } else {
      $bgImage['data-bg'] = $bgImage;
    }
  }

  $attributes = implode(' ', $attributes);

  $html = ['<' . implode(' ', array_filter([$tagName, $class, $attributes]))];

  $isNonClosingTag = isNonClosingTag($tagName);

  if (!$isNonClosingTag) {
    $html[] = '>';

  } else {
    $html[] = ' />';
    return implode('', array_filter($html));
  }

  $html[] = implode('', content($content));

  $html[] = '</' . $tagName . '>';

  return implode('', array_filter($html));
}

function div(
  Closure|string|array|null $content = null,
  string|array              $class = null,
  array                     $attributes = [],
  File|string               $bgImage = null
): string
{
  return tag('div', $content, $class, $attributes, $bgImage);
}

function span(
  Closure|string|array|null $content = null,
  string|array              $class = null,
  array                     $attributes = [],
  File|string               $bgImage = null
): string
{
  return tag('span', $content, $class, $attributes, $bgImage);
}

function form(
  Closure|string|array|null $content = null,
  string|array              $class = null,
  array                     $attributes = [],
  string                    $method = 'get',
  string                    $action = '',
  File|string               $bgImage = null
): string
{
  $attributes = $attributes ?? [];

  if ($method) {
    $attributes['method'] = $method;
  }

  if ($action) {
    $attributes['action'] = $action;
  }

  return tag('form', $content, $class, $attributes, $bgImage);
}

function i(
  Closure|string|array|null $content = null,
  string|array              $class = null,
  array                     $attributes = [],
  File|string               $bgImage = null
): string
{
  return tag('i', $content, $class, $attributes, $bgImage);
}

function a(
  string                    $href = null,
  Closure|string|array|null $content = null,
  string|array              $class = null,
  array                     $attributes = [],
  File|string               $bgImage = null
): string
{
  $attributes = $attributes ?? [];
  $attributes['href'] = $href;

  if (is_string($content)) {
    $attributes['title'] = mb_substr(strip_tags($content), 0, 100);
  }

  return tag('a', $content, $class, $attributes, $bgImage);
}

function ul(
  Closure|string|array|null $content = null,
  string|array              $class = null,
  array                     $attributes = [],
  File|string               $bgImage = null
): string
{
  return tag('ul', $content, $class, $attributes, $bgImage);
}

function li(
  Closure|string|array|null $content = null,
  string|array              $class = null,
  array                     $attributes = [],
  File|string               $bgImage = null
): string
{
  return tag('li', $content, $class, $attributes, $bgImage);
}