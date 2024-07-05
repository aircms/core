<?php

declare(strict_types=1);

namespace Air\Crud;

use Exception;

class Generator
{
  /**
   * @var array
   */
  public array $config = [];

  /**
   * @var string
   */
  public string $type;

  /**
   * @var string
   */
  public string $icon;

  /**
   * @var string
   */
  public string $section;

  /**
   * @var string
   */
  public string $name;

  /**
   * @param array $config
   * @param string $type
   * @param string $section
   * @param string $name
   * @throws Exception
   */
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

  /**
   * @return bool
   * @throws Exception
   */
  public function model(): bool
  {
    $namespace = $this->config['air']['loader']['namespace'] . "\\Model";
    $name = ucfirst(str_replace(' ', '', $this->name));
    $title = $this->name;

    return !!$this->gen($namespace, $name, $title, 'model', 'Model');
  }

  /**
   * @return bool
   * @throws Exception
   */
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

  /**
   * @return bool
   */
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

  /**
   * @param string $namespace
   * @param string $name
   * @param string $title
   * @param string $subject
   * @param string $dest
   * @return int
   * @throws Exception
   */
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
