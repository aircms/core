<?php

declare(strict_types=1);

use Air\Core\Exception\ClassWasNotFound;
use Air\Model\Exception\CallUndefinedMethod;
use Air\Model\Exception\ConfigWasNotProvided;
use Air\Model\Exception\DriverClassDoesNotExists;
use Air\Model\Exception\DriverClassDoesNotExtendsFromDriverAbstract;
use Air\Type\FaIcon;

/**
 * @param string|FaIcon|null $icon
 * @param string|null $style
 * @param string|array $data
 * @param array $attributes
 * @param string $tag
 * @param string|array|null $class
 * @return string
 * @throws CallUndefinedMethod
 * @throws ClassWasNotFound
 * @throws ConfigWasNotProvided
 * @throws DriverClassDoesNotExists
 * @throws DriverClassDoesNotExtendsFromDriverAbstract
 * @throws ReflectionException
 * @throws Throwable
 */
function faIcon(
  string|FaIcon $icon = null,
  string        $style = null,
  string|array  $data = [],
  array         $attributes = [],
  string        $tag = 'i',
  string|array  $class = null,
): string
{
  if ($icon === null) {
    return '';
  }

  if (is_string($icon)) {
    $icon = new FaIcon([
      'icon' => $icon,
    ]);
  }

  $class = (array)$class ?? [];

  if ($icon->isBrand()) {
    $class[] = 'fa-brands';

  } else {
    $class[] = $style ?? $icon->getStyle();
  }

  $class[] = 'fa-' . $icon->getIcon();

  $class = array_unique($class);

  return tag(
    tagName: $tag,
    class: $class,
    data: $data,
    attributes: $attributes,
  );
}