<?php

namespace Air\Core;

/**
 * Class Response
 * @package Air
 */
class Response
{
  /**
   * @var int
   */
  private $_statusCode = 200;

  /**
   * @var string
   */
  private $_statusMessage = '';

  /**
   * @var mixed
   */
  private $_body = null;

  /**
   * @var array
   */
  private $_headers = [];

  /**
   * @return int
   */
  public function getStatusCode(): int
  {
    return $this->_statusCode;
  }

  /**
   * @param int $statusCode
   */
  public function setStatusCode(int $statusCode)
  {
    $this->_statusCode = $statusCode;
  }

  /**
   * @return string
   */
  public function getStatusMessage(): string
  {
    return $this->_statusMessage;
  }

  /**
   * @param string $statusMessage
   */
  public function setStatusMessage(string $statusMessage)
  {
    $this->_statusMessage = $statusMessage;
  }

  /**
   * @return mixed
   */
  public function getBody()
  {
    return $this->_body;
  }

  /**
   * @param mixed $body
   */
  public function setBody($body)
  {
    $this->_body = $body;
  }

  /**
   * @return array
   */
  public function getHeaders(): array
  {
    return $this->_headers;
  }

  /**
   * @param array $headers
   */
  public function setHeaders(array $headers)
  {
    $this->_headers = $headers;
  }

  /**
   * @param array $headers
   */
  public function addHeaders(array $headers)
  {
    foreach ($headers as $key => $val) {
      $this->_headers[$key] = $val;
    }
  }

  /**
   * @param string $key
   * @param string $value
   */
  public function setHeader(string $key, string $value)
  {
    $this->_headers[$key] = $value;
  }
}