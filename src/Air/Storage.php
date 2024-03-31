<?php

declare(strict_types=1);

namespace Air;

use Air\Core\Front;
use Air\Form\Element\DateTime;
use Air\Http\Response;
use Air\Http\Request;
use Air\Type\File;
use Exception;

class Storage
{
  /**
   * @param string $path
   * @param string $name
   * @param bool $recursive
   * @return bool
   * @throws Core\Exception\ClassWasNotFound
   */
  public static function createFolder(string $path, string $name, bool $recursive = false): bool
  {
    return self::action('createFolder', [
      'path' => $path,
      'name' => $name,
      'recursive' => $recursive
    ])->isOk();
  }

  /**
   * @param string $path
   * @return bool
   * @throws Core\Exception\ClassWasNotFound
   */
  public static function deleteFolder(string $path): bool
  {
    return self::action('deleteFolder', ['path' => $path])->isOk();
  }

  /**
   * @param string $path
   * @param string $url
   * @param string|null $name
   * @return File
   * @throws Core\Exception\ClassWasNotFound
   * @throws Exception
   */
  public static function uploadByUrl(string $path, string $url, ?string $name = null): File
  {
    $r = self::action('uploadByUrl', [
      'path' => $path,
      'url' => $url,
      'name' => $name
    ]);

    if (!$r->isOk()) {
      throw new Exception($r->body['message']);
    }

    return new File($r->body);
  }

  /**
   * @param string $path
   * @return bool
   * @throws Core\Exception\ClassWasNotFound
   */
  public static function deleteFile(string $path): bool
  {
    return self::action('deleteFile', [
      'path' => $path
    ])->isOk();
  }

  /**
   * @param string $path
   * @param array $files
   * @return File[]
   * @throws Core\Exception\ClassWasNotFound
   */
  public static function uploadFiles(string $path, array $files): array
  {
    $storageConfig = Front::getInstance()->getConfig()['air']['storage'];
    $url = $storageConfig['url'] . '/api/uploadFile';

    $response = Request::run($url, [
      'body' => [
        'path' => $path,
        'key' => $storageConfig['key'],
      ],
      'files' => [
        'files' => $files
      ]
    ]);

    $files = [];

    foreach ($response->body as $file) {
      $files[] = new File($file);
    }

    return $files;
  }

  /**
   * @param string $endpoint
   * @param array|null $params
   * @return Response
   * @throws Core\Exception\ClassWasNotFound
   */
  public static function action(string $endpoint, ?array $params = []): Response
  {
    $storageConfig = Front::getInstance()->getConfig()['air']['storage'];

    $url = $storageConfig['url'] . '/api/' . $endpoint;
    $params['key'] = $storageConfig['key'];

    return Request::run($url, [
      'body' => $params,
      'method' => Request::POST
    ]);
  }
}
