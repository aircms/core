<?php

declare(strict_types=1);

namespace Air;

use Air\Core\Front;
use Air\Http\Request;
use Air\Http\Response;
use Air\Type\File;
use Exception;
use Throwable;

class Storage
{
  public static function createFolder(
    string $path,
    string $name,
    ?bool  $recursive = false,
    ?bool  $sharding = false
  ): string|false
  {
    $path = $path . $name;

    if ($sharding) {
      $hash = md5(microtime());

      $dir1 = substr($hash, 0, 2);
      $dir2 = substr($hash, 2, 2);

      $path = $path . '/' . $dir1 . '/' . $dir2;
    }

    return self::action('createFolder', [
      'path' => $path,
      'name' => $name,
      'recursive' => $recursive
    ])->isOk() ? $path : false;
  }

  public static function deleteFolder(string $path): bool
  {
    return self::action('deleteFolder', ['path' => $path])->isOk();
  }

  public static function uploadByUrl(string $path, string $url, ?string $name = null, ?bool $sharding = false): File
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

  public static function deleteFile(string $path): bool
  {
    return self::action('deleteFile', ['path' => $path])->isOk();
  }

  /**
   * @param string $path
   * @param File[] $files
   * @return array
   * @throws Exception
   */
  public static function uploadFiles(string $path, array $files, ?bool $sharding = false): array
  {
    $path = self::createFolder('/', $path, true, $sharding);

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
   * @param string $path
   * @param array $datum
   * @return array|File[]
   * @throws Exception
   */
  public static function uploadDatum(string $path, array $datum, ?bool $sharding = false): array
  {
    $path = self::createFolder('/', $path, true, $sharding);

    $response = self::action('uploadDatum', [
      'path' => $path,
      'datum' => $datum,
    ]);

    if (!$response->isOk()) {
      throw new Exception($response->body['message']);
    }

    $files = [];
    foreach ($response->body as $file) {
      $files[] = new File($file);
    }
    return $files;
  }

  /**
   * @param string $path
   * @param array $datum
   * @return File[]
   * @throws Exception
   */
  public static function uploadBase64Datum(string $path, array $datum, ?bool $sharding = false): array
  {
    foreach ($datum as $index => $image) {
      $datum[$index] = [
        'type' => 'base64',
        'data' => $image
      ];
    }
    return self::uploadDatum($path, $datum, $sharding);
  }

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

  /**
   * @param array $paths
   * @return File[]
   * @throws Exception
   */
  public static function info(array $paths): array
  {
    $storageConfig = Front::getInstance()->getConfig()['air']['storage'];
    $url = $storageConfig['url'] . '/api/info';

    $response = Request::run($url, [
      'method' => Request::POST,
      'body' => [
        'key' => $storageConfig['key'],
        'paths' => $paths,
      ]
    ]);

    $files = [];
    foreach ($response->body as $file) {
      $files[] = new File($file);
    }
    return $files;
  }

  public static function annotation(
    string $folder,
    string $fileName,
    string $title,
    string $backColor,
    string $frontColor
  ): ?File
  {
    $storageConfig = Front::getInstance()->getConfig()['air']['storage'];
    $url = $storageConfig['url'] . '/api/annotation';

    $params['key'] = $storageConfig['key'];
    $params['folder'] = $folder;
    $params['fileName'] = $fileName;
    $params['title'] = $title;
    $params['backColor'] = $backColor;
    $params['frontColor'] = $frontColor;

    $response = Request::run($url, [
      'body' => $params,
      'method' => Request::POST
    ]);

    if (!$response->isOk()) {
      return null;
    }

    return new File($response->body);
  }

  public static function isImage(string|array $input): bool
  {
    try {
      if (is_array($input)
        && str_starts_with($input['src'], Front::getInstance()->getConfig()['air']['storage']['url'])
        && str_starts_with($input['thumbnail'], Front::getInstance()->getConfig()['air']['storage']['url'])) {
        return true;
      }
    } catch (Throwable) {
      return false;
    }

    try {
      if (stripos($input, 'data:') === 0) {
        $commaPos = strpos($input, ',');
        if ($commaPos === false) {
          return false;
        }
        $input = substr($input, $commaPos + 1);
      }

      $input = preg_replace('/\s+/', '', $input);

      $binary = base64_decode($input, true);
      if ($binary === false || $binary === '') {
        return false;
      }

      return !!getimagesizefromstring($binary);

    } catch (Throwable) {
      return false;
    }
  }
}
