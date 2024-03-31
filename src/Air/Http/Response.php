<?php

declare(strict_types=1);

namespace Air\Http;

class Response
{
  /**
   * @var int
   */
  public int $code = 0;

  /**
   * @var array
   */
  public array $header = [];

  /**
   * @var mixed|string
   */
  public mixed $body = '';

  /**
   * @param string|null $httpResponse
   */
  public function __construct(?string $httpResponse = null)
  {
    if ($httpResponse) {
      $responseParts = explode("\r\n\r\n", $httpResponse);
      $headers = explode("\n", $responseParts[0]);

      $this->code = intval(trim(explode(' ', $headers[0])[1]));
      unset($headers[0]);

      foreach ($headers as $header) {
        $header = explode(':', $header);
        $key = trim($header[0]);
        unset($header[0]);
        $value = trim(implode(':', $header));

        $this->header[strtolower($key)] = $value;
      }

      $this->body = trim($responseParts[1]);

      if (str_contains($this->header['content-type'] ?? '', 'application/json')) {
        $this->body = json_decode($this->body, true);
      }
    }
  }

  /**
   * @return bool
   */
  public function isOk(): bool
  {
    return $this->code >= 200 && $this->code < 300;
  }
}