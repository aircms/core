<?php

declare(strict_types=1);

namespace Air\Type;

use Air\Model\ModelAbstract;
use ReflectionClass;
use ReflectionProperty;
use Throwable;

abstract class TypeAbstract
{
  public function __construct(?array $item = [])
  {
    foreach (array_keys(get_class_vars(static::class)) as $var) {

      if (isset($item[$var])) {
        $value = $item[$var];

        list($type, $isArray) = $this->getPropertyType($var);

        if (class_exists($type)) {

          if ($isArray) {
            $this->{$var} = [];

            foreach ($value as $datum) {

              if (is_subclass_of($type, TypeAbstract::class)) {
                if (is_object($datum)) {
                  $this->{$var}[] = $datum;
                } else {
                  $this->{$var}[] = new $type($datum);
                }

              } elseif (is_subclass_of($type, ModelAbstract::class)) {
                if (is_object($datum)) {
                  $this->{$var}[] = $datum;
                } else {
                  $this->{$var}[] = $type::singleOne([(new $type)->getMeta()->getPrimary() => $datum]);
                }
              }
            }
          } else {
            if (is_subclass_of($type, TypeAbstract::class)) {
              if (is_object($value)) {
                $this->{$var} = $value;
              } else {
                $this->{$var} = new $type($value);
              }

            } elseif (is_subclass_of($type, ModelAbstract::class)) {
              if (is_object($value)) {
                $this->{$var} = $value;
              } else {
                $this->{$var} = $type::singleOne([(new $type)->getMeta()->getPrimary() => $value]);
              }
            }
          }
        } else {
          $this->{$var} = $value;
        }
      }
    }
  }

  public function getUsedNamespaces(mixed $class): array
  {
    $usedNamespaces = [
      'namespace' => '',
      'uses' => []
    ];

    $r = new ReflectionClass($class::class);
    $filePath = $r->getFileName();

    foreach (explode("\n", file_get_contents($filePath)) as $line) {
      if (str_starts_with(trim($line), 'use ')) {
        $usedNamespaces['uses'][] = str_replace(';', '', trim(explode('use', $line)[1]));
        continue;
      }

      if (str_starts_with(trim($line), 'namespace ')) {
        $usedNamespaces['namespace'] = str_replace(';', '', trim(explode('namespace', $line)[1]));
      }
    }
    return $usedNamespaces;
  }

  public function getPropertyType(string $property): ?array
  {
    $rp = new ReflectionProperty(static::class, $property);
    $type = $rp->getType()->getName();

    if (class_exists($type)) {
      return [$type, false];
    }

    try {
      $docBlocks = explode('/', str_replace('*', '', $rp->getDocComment()));
      foreach ($docBlocks as $docBlock) {
        if (str_contains($docBlock, '@var')) {
          $type = trim(str_replace('[]', '', explode('|', trim(str_replace('@var', '', $docBlock)))[0]));

          if (class_exists($type)) {
            return [$type, true];
          }

          $namespaces = $this->getUsedNamespaces(new static());

          if (class_exists($namespaces['namespace'] . '\\' . $type)) {
            $type = $namespaces['namespace'] . '\\' . $type;

          } else {
            foreach ($namespaces['uses'] as $namespace) {
              if (str_contains($namespace, $type)) {
                $type = $namespace;
                break;
              }
            }
          }
          if (class_exists($type)) {
            return [$type, true];
          }
        }
      }
    } catch (Throwable) {
    }

    $type = $rp->getType()->getName();

    return [$type, $type === 'array'];
  }

  public function toArray(): array
  {
    $array = [];
    foreach (get_class_vars(static::class) as $var => $value) {
      if (is_object($this->{$var})) {
        if (method_exists($this->{$var}, 'toArray')) {
          $value = $this->{$var}->toArray();
        } else {
          $value = (array)$this->{$var};
        }
      } else {
        $value = $this->{$var};
      }
      $array[$var] = $value;
    }
    return $array;
  }

  public function toRaw(): array
  {
    $array = [];

    foreach (get_class_vars(static::class) as $var => $value) {
      $value = $this->{$var};

      list($type, $isArray) = $this->getPropertyType($var);

      if (class_exists($type)) {

        if ($isArray) {
          $array[$var] = [];

          foreach ($value as $datum) {

            if (is_subclass_of($type, TypeAbstract::class)) {
              if (is_object($datum)) {
                $array[$var][] = $datum->toRaw();
              } else {
                $array[$var][] = $datum;
              }

            } elseif (is_subclass_of($type, ModelAbstract::class)) {
              if (is_object($datum)) {
                $array[$var][] = $datum->{(new $type)->getMeta()->getPrimary()};
              } else {
                $array[$var][] = $datum;
              }
            }
          }
        } else {
          if (is_subclass_of($type, TypeAbstract::class)) {
            if (is_object($value)) {
              $array[$var] = $value->toRaw();
            } else {
              $array[$var] = $value;
            }

          } elseif (is_subclass_of($type, ModelAbstract::class)) {
            if (is_object($value)) {
              $array[$var] = $value->{(new $type)->getMeta()->getPrimary()};
            } else {
              $array[$var] = $value;
            }
          }
        }
      } else {
        $array[$var] = $value;
      }
    }
    return $array;
  }
}