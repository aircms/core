<?php

declare(strict_types=1);

namespace Air\Http;

use Exception;

class Request
{
  const string GET = 'GET';
  const string POST = 'POST';

  /**
   * @var string
   */
  public string $url;

  /**
   * @var string
   */
  public string $method = self::GET;

  /**
   * @var array
   */
  public array $get = [];

  /**
   * @var array
   */
  public array $headers = [];

  /**
   * @var array
   */
  public array $cookies = [];

  /**
   * @var string|null
   */
  public ?string $bearer = null;

  /**
   * @var mixed|null
   */
  public mixed $body = null;

  /**
   * @var int
   */
  public int $timeout = 30;

  /**
   * @var array
   */
  public array $files = [];

  /**
   * @param string $url
   * @return $this
   */
  public function url(string $url): self
  {
    $this->url = $url;
    return $this;
  }

  /**
   * @param string $method
   * @return self
   */
  public function method(string $method): self
  {
    $this->method = $method;
    return $this;
  }

  /**
   * @param array $get
   * @return $this
   */
  public function get(array $get): self
  {
    $this->get = [...$this->get, ...$get];
    return $this;
  }

  /**
   * @param array $header
   * @return $this
   */
  public function headers(array $header): self
  {
    $this->headers = [...$this->headers, ...$header];
    return $this;
  }

  /**
   * @param array $cookies
   * @return $this
   */
  public function cookies(array $cookies): self
  {
    $this->cookies = [...$this->cookies, ...$cookies];
    return $this;
  }

  /**
   * @param string $bearer
   * @return self
   */
  public function bearer(string $bearer): self
  {
    $this->bearer = $bearer;
    return $this;
  }

  /**
   * @param array $body
   * @return $this
   */
  public function body(mixed $body): self
  {
    $this->body = $body;
    return $this;
  }

  /**
   * @param string $type
   * @return self
   */
  public function type(string $type): self
  {
    $types = [
      'json' => 'application/json',
      'formData' => 'multipart/form-data',
      'xFormData' => 'application/x-www-form-urlencoded',
    ];

    $this->headers['content-type'] = $types[$type] ?? $type;
    return $this;
  }

  /**
   * @param int $timeout
   * @return $this
   */
  public function timeout(int $timeout): self
  {
    $this->timeout = $timeout;
    return $this;
  }

  /**
   * @param string $name
   * @param string|array $fileOrFiles
   * @return $this
   */
  public function file(string $name, string|array $fileOrFiles): self
  {
    $this->files[$name] = $fileOrFiles;
    return $this;
  }

  /**
   * @param array $files
   * @return self
   */
  public function files(array $files): self
  {
    foreach ($files as $key => $value) {
      $this->file($key, $value);
    }
    return $this;
  }

  /**
   * @return Response
   * @throws Exception
   */
  public function do(): Response
  {
    $ch = curl_init();

    $url = $this->url;
    if (count($this->get)) {
      $url .= '?' . http_build_query($this->get);
    }

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
    curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

    $headers = $this->headers;

    if ($this->bearer) {
      $headers['Authorization'] = 'Bearer ' . $this->bearer;
    }

    if (count($this->files)) {
      foreach ($this->files as $name => $fileOrFiles) {
        if (is_array($fileOrFiles)) {
          foreach ($fileOrFiles as $index => $file) {
            $this->body[$name . '[' . $index . ']'] = curl_file_create($file);
          }
        } else {
          $this->body[$name] = curl_file_create($fileOrFiles);
        }
      }
      $this->method = self::POST;
      $headers['content-type'] = 'multipart/form-data';
    }

    if (count($headers)) {
      $_headers = [];
      foreach ($headers as $name => $header) {
        $_headers[] = ucfirst($name) . ": " . $header;
      }

      curl_setopt($ch, CURLOPT_HTTPHEADER, $_headers);
    }

    if ($this->method === self::POST) {
      $body = self::convertDataToContentType(
        $headers['content-type'] ?? '',
        $this->body
      );
      curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
    }

    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $this->method);

    $response = curl_exec($ch);
    curl_close($ch);

    if ($response === false) {
      throw new Exception('Curl error: ' . curl_error($ch));
    }

    return new Response($response);
  }

  /**
   * @param string $contentType
   * @param mixed $data
   * @return mixed
   */
  public static function convertDataToContentType(string $contentType, mixed $data): mixed
  {
    return match ($contentType) {
      'application/json' => json_encode($data),
      'multipart/form-data' => $data,
      default => http_build_query($data),
    };
  }

  /**
   * @param string $url
   * @param array $options
   * @return Response
   * @throws Exception
   */
  public static function run(string $url, array $options = []): Response
  {
    $notAllowedMethods = [
      'do',
      'convertDataToContentType',
      '__callStatic',
      'run'
    ];

    $request = new self();
    $request->url($url);

    foreach ($options as $key => $value) {
      if (in_array($key, $notAllowedMethods)) {
        throw new Exception("Method $key is now allowed");
      }

      if (method_exists($request, $key)) {
        $request->{$key}($value);
      }
    }

    return $request->do();
  }

  /**
   * @param string $url
   * @param mixed|null $data
   * @return Response
   * @throws Exception
   */
  public static function postJson(string $url, mixed $data = null): Response
  {
    return self::run($url, [
      'method' => self::POST,
      'type' => 'json',
      'body' => $data
    ]);
  }

  /**
   * @param string $url
   * @param array $get
   * @return Response
   * @throws Exception
   */
  public static function getQuery(string $url, array $get = []): Response
  {
    return self::run($url, [
      'get' => $get
    ]);
  }

  /**
   * @param string $url
   * @param array $get
   * @return Response
   * @throws Exception
   */
  public static function postQuery(string $url, array $get = []): Response
  {
    return self::run($url, [
      'method' => self::POST,
      'type' => 'json',
      'get' => $get
    ]);
  }
}