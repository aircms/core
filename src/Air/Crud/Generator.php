<?php

declare(strict_types=1);

namespace Air\Crud;

use Exception;

class Generator
{
  public array $config = [];
  public string $type;
  public string $icon;
  public string $section;
  public string $name;

  public function __construct(
    array  $config,
    string $icon,
    string $type,
    string $section,
    string $name
  )
  {
    if (!in_array($type, ['multiple', 'single'])) {
      throw new Exception('Unsupported type: ' . $type);
    }

    $this->config = $config;

    $this->icon = $icon;
    $this->type = $type;
    $this->section = $section;
    $this->name = $name;
  }

  public function model(): bool
  {
    $namespace = $this->config['air']['loader']['namespace'] . "\\Model";
    $name = ucfirst(str_replace(' ', '', $this->name));
    $title = $this->name;

    return !!$this->gen($namespace, $name, $title, 'model', 'Model');
  }

  public function controller(): bool
  {
    $namespace = $this->config['air']['loader']['namespace'] . '\Module\Admin\Controller';
    $name = ucfirst(str_replace(' ', '', $this->name));
    $title = $this->name;

    if ($this->type === 'multiple') {
      $subject = 'controller-' . $this->config['air']['admin']['locale'];
    } else {
      $subject = 'controller';
    }

    return !!$this->gen($namespace, $name, $title, $subject, 'Module/Admin/Controller');
  }

  public function config(): bool
  {
    $nav = require 'config/nav.php';

    $defaultSection = [
      'title' => $this->section,
      'icon' => $this->icon,
      'items' => [[
        'title' => $this->name,
        'icon' => $this->icon,
        'url' => ['controller' => lcfirst(str_replace(' ', '', $this->name))]
      ]]
    ];

    $isAdded = false;
    foreach ($nav as $index => $section) {
      if (trim(strtolower($section['title'] ?? '')) === trim(strtolower($this->section))) {
        $nav[$index]['items'] = $nav[$index]['items'] ?? [];
        $nav[$index]['items'][] = $defaultSection['items'][0];
        $isAdded = true;
        break;
      }
    }

    if (!$isAdded) {
      $nav[] = $defaultSection;
    }

    $nav = '<' . '?php' . "\n\nreturn " . str_replace(
        ['{', '}', '":', '"', '    '],
        ['[', ']', '" =>', "'", '  '],
        json_encode($nav, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
      ) . ';';

    return !!file_put_contents('config/nav.php', $nav);
  }

  private function gen(string $namespace, string $name, string $title, string $subject, string $dest): int
  {
    $dir = __DIR__;

    $fileName = ucfirst($name);
    $destFile = "{$this->config['air']['loader']['path']}/{$dest}/{$fileName}.php";

    $template = str_replace(
      ['{namespace}', '{name}', '{title}'],
      [$namespace, $name, $title],
      file_get_contents("{$dir}/Generator/{$this->type}/{$subject}.tpl")
    );

    try {
      return file_put_contents($destFile, $template);
    } catch (Exception $e) {
      throw new Exception("Cant write a {$subject}. Because of {$e->getMessage()}");
    }
  }
}
