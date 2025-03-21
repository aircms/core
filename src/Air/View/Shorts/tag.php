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
    'input',
    'meta',
    'base',
    'br',
    'hr',
    'link',
    'img',
    'source',
  ]);
}

/**
 * @param Closure|string|array|Generator|null $content
 * @return string
 */
function render(Closure|string|array|null|Generator $content = null): string
{
  return implode('', $content);
}

/**
 * @param Closure|string|array|Generator|null $content
 * @return array
 */
function content(Closure|string|array|null|Generator $content = null): array
{
  if ($content) {

    if (is_string($content)) {
      $content = (array)$content;

    } else if ($content instanceof Generator) {
      $contentArray = [];
      foreach ($content as $item) {
        $contentArray[] = $item;
      }

      $content = $contentArray;

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
 * @param File|string $image
 * @return string
 * @throws ClassWasNotFound
 */
function image(File|string $image): string
{
  if ($image instanceof File) {
    return $image->getSrc();
  } else {
    return $image;
  }
}

/**
 * @param string $tagName
 * @param Closure|string|array|Generator|null $content
 * @param string|array|null $class
 * @param array|string|null $attributes
 * @param File|string|null $bgImage
 * @return string
 * @throws ClassWasNotFound
 */
function tag(
  string                              $tagName,
  Closure|string|array|null|Generator $content = null,
  string|array                        $class = null,
  array|string                        $attributes = null,
  array|string                        $data = null,
  File|string                         $bgImage = null
): string
{
  $tagName = trim(strtolower($tagName));

  $class = (array)$class;
  $attributes = (array)$attributes;
  $data = (array)$data;

  if ($bgImage) {
    $class[] = 'bg-image';
    $attributes['style'] = (array)($attributes['style'] ?? []) ?? [];
    $attributes['style'][] = "background-image: url('" . image($bgImage) . "')";
    $attributes['style'] = implode('; ', $attributes['style']);
  }

  if (count($class)) {
    $class = 'class="' . implode(' ', array_filter($class)) . '"';
  }

  foreach ($data as $key => $value) {
    if (!is_int($key)) {
      $attributes[] = 'data-' . $key . '="' . $value . '"';
    } else {
      $attributes[] = 'data-' . $value;
    }
  }

  foreach ($attributes as $key => $value) {
    if (!is_int($key)) {
      $attributes[] = $key . '="' . $value . '"';
      unset($attributes[$key]);
    } else {
      $attributes[] = $value;
    }
  }

  $attributes = implode(' ', array_filter($attributes));

  $html = ['<' . implode(' ', array_filter([$tagName, $class, $attributes]))];

  $isNonClosingTag = isNonClosingTag($tagName);

  if (!$isNonClosingTag) {
    $html[] = '>';

  } else {
    $html[] = ' />';
    return implode('', array_filter($html));
  }

  try {
    $html[] = implode('', content($content));
  } catch (Throwable $e) {
    echo "----Опять проблемы с контентом в TAG----";
    throw $e;
    var_dump($content);
    die();
  }

  $html[] = '</' . $tagName . '>';

  return implode('', array_filter($html));
}

function div(
  string|array                        $class = null,
  Closure|string|array|null|Generator $content = null,
  array|string                        $attributes = null,
  array|string                        $data = null,
  File|string                         $bgImage = null
): string
{
  return tag(
    tagName: 'div',
    content: $content,
    class: $class,
    attributes: $attributes,
    data: $data,
    bgImage: $bgImage
  );
}

function span(
  Closure|string|array|null $content = null,
  string|array              $class = null,
  array|string              $attributes = null,
  array|string              $data = null,
  File|string               $bgImage = null
): string
{
  return tag(
    tagName: 'span',
    content: $content,
    class: $class,
    attributes: $attributes,
    data: $data,
    bgImage: $bgImage
  );
}

function form(
  Closure|string|array|null $content = null,
  string|array              $class = null,
  array|string              $attributes = null,
  array|string              $data = null,
  string                    $method = 'get',
  string                    $action = '',
  File|string               $bgImage = null
): string
{
  $attributes = (array)$attributes ?? [];

  if ($method) {
    $attributes['method'] = $method;
  }

  if ($action) {
    $attributes['action'] = $action;
  }

  return tag(
    tagName: 'form',
    content: $content,
    class: $class,
    attributes: $attributes,
    data: $data,
    bgImage: $bgImage
  );
}

function i(
  Closure|string|array|null $content = null,
  string|array              $class = null,
  array|string              $attributes = null,
  File|string               $bgImage = null
): string
{
  return tag(
    tagName: 'i',
    content: $content,
    class: $class,
    attributes: $attributes,
    bgImage: $bgImage
  );
}

function a(
  string                    $href = null,
  Closure|string|array|null $content = null,
  string|array              $class = null,
  array|string              $attributes = null,
  array|string              $data = null,
  File|string               $bgImage = null,
  string                    $title = null,
  bool                      $openInNewWindow = false
): string
{
  $attributes = (array)$attributes ?? [];
  $data = (array)$data ?? [];

  if ($href) {
    $attributes['href'] = $href;
  }

  if (is_string($content) && !$title) {
    $attributes['title'] = htmlspecialchars(mb_substr(strip_tags($content), 0, 100));
  } else if ($title) {
    $attributes['title'] = htmlspecialchars($title);
  }

  if ($openInNewWindow) {
    $attributes['target'] = '_blank';
  }

  return tag(
    tagName: 'a',
    content: $content,
    class: $class,
    attributes: $attributes,
    data: $data,
    bgImage: $bgImage
  );
}

function ul(
  Closure|string|array|null $content = null,
  string|array              $class = null,
  array|string              $attributes = null,
  File|string               $bgImage = null
): string
{
  return tag(
    tagName: 'ul',
    content: $content,
    class: $class,
    attributes: $attributes,
    bgImage: $bgImage
  );
}

function li(
  Closure|string|array|null $content = null,
  string|array              $class = null,
  array|string              $attributes = null,
  File|string               $bgImage = null
): string
{
  return tag(
    tagName: 'li',
    content: $content,
    class: $class,
    attributes: $attributes,
    bgImage: $bgImage
  );
}

function script(
  Closure|string|array|null $content = null,
  bool                      $async = false,
  bool                      $defer = false,
  string                    $src = null,
  array|string              $attributes = null,
): string
{
  $attributes = (array)$attributes ?? [];

  if ($async) {
    $attributes['async'] = '';
  }

  if ($defer) {
    $attributes['defer'] = '';
  }

  if ($src) {
    $attributes['src'] = $src;
  }

  return tag(
    tagName: 'script',
    content: $content,
    attributes: $attributes
  );
}

function img(
  File|string  $src = null,
  string|array $class = null,
  array|string $attributes = null,
  string       $alt = null,
  string       $title = null
): string
{
  $attributes = (array)$attributes ?? [];

  if ($src instanceof File) {
    $attributes['src'] = $src->getSrc();
    $attributes['alt'] = $src->getAlt();
    $attributes['title'] = $src->getTitle();
  } else {
    $attributes['src'] = $src;
  }

  if ($alt) {
    $attributes['alt'] = $alt;
  }

  if ($title) {
    $attributes['title'] = $title;
  }

  return tag(
    tagName: 'img',
    class: $class,
    attributes: $attributes
  );
}

function h1(
  string|array              $class = null,
  Closure|string|array|null $content = null,
  array|string              $attributes = null,
  File|string               $bgImage = null
): string
{
  return tag(
    tagName: 'h1',
    content: $content,
    class: $class,
    attributes: $attributes,
    bgImage: $bgImage
  );
}

function h2(
  string|array              $class = null,
  Closure|string|array|null $content = null,
  array|string              $attributes = null,
  File|string               $bgImage = null
): string
{
  return tag(
    tagName: 'h2',
    content: $content,
    class: $class,
    attributes: $attributes,
    bgImage: $bgImage
  );
}

function h3(
  string|array              $class = null,
  Closure|string|array|null $content = null,
  array|string              $attributes = null,
  File|string               $bgImage = null
): string
{
  return tag(
    tagName: 'h3',
    content: $content,
    class: $class,
    attributes: $attributes,
    bgImage: $bgImage
  );
}

function h4(
  string|array              $class = null,
  Closure|string|array|null $content = null,
  array|string              $attributes = null,
  File|string               $bgImage = null
): string
{
  return tag(
    tagName: 'h4',
    content: $content,
    class: $class,
    attributes: $attributes,
    bgImage: $bgImage
  );
}

function h5(
  string|array              $class = null,
  Closure|string|array|null $content = null,
  array|string              $attributes = null,
  File|string               $bgImage = null
): string
{
  return tag(
    tagName: 'h5',
    content: $content,
    class: $class,
    attributes: $attributes,
    bgImage: $bgImage
  );
}

function h6(
  string|array              $class = null,
  Closure|string|array|null $content = null,
  array|string              $attributes = null,
  File|string               $bgImage = null
): string
{
  return tag(
    tagName: 'h6',
    content: $content,
    class: $class,
    attributes: $attributes,
    bgImage: $bgImage
  );
}

function iframe(
  string       $src,
  string|array $class = null,
  array|string $attributes = null,
): string
{
  $attributes = $attributes ?? [];
  $attributes['src'] = $src;

  return tag(
    tagName: 'iframe',
    class: $class,
    attributes: $attributes
  );
}

function doctype()
{
  return '<!DOCTYPE html>';
}

function main(
  string|array              $class = null,
  array|string              $attributes = null,
  array|string              $data = null,
  Closure|string|array|null $content = null,
)
{
  $attributes = $attributes ?? [];
  $attributes['role'] = 'main';

  return tag(
    tagName: 'main',
    attributes: $attributes,
    class: $class,
    data: $data,
    content: $content
  );
}

function footer(
  string|array              $class = null,
  array|string              $attributes = null,
  array|string              $data = null,
  Closure|string|array|null $content = null,
): string
{
  return tag(
    tagName: 'footer',
    attributes: $attributes,
    class: $class,
    data: $data,
    content: $content
  );
}

function video(
  File|string  $src = null,
  string|array $class = null,
  array|string $attributes = null
)
{
  $type = 'video/mp4';

  if ($src instanceof File) {
    $type = $src->getMime();
    $src = $src->getSrc();
  }

  return tag(
    tagName: 'video',
    class: $class,
    attributes: $attributes,
    content: tag(
      tagName: 'source',
      attributes: [
        'src' => $src,
        'type' => $type
      ]
    )
  );
}

function section(
  Closure|string|array|null|Generator $content = null,
  string|array                        $class = null,
  array|string                        $attributes = null,
  array|string                        $data = null,
  File|string                         $bgImage = null
): string
{
  return tag(
    tagName: 'section',
    content: $content,
    class: $class,
    attributes: $attributes,
    data: $data,
    bgImage: $bgImage
  );
}

function br()
{
  return tag('br');
}

function p(
  Closure|string|array|null|Generator $content = null,
  string|array                        $class = null,
  array|string                        $attributes = null,
  array|string                        $data = null,
): string
{
  return tag(
    tagName: 'p',
    content: $content,
    class: $class,
    attributes: $attributes,
    data: $data
  );
}