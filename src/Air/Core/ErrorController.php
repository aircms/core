<?php

declare(strict_types=1);

namespace Air\Core;

use Error;
use Exception;

class ErrorController extends Controller
{
  /**
   * @var Error|Exception|null
   */
  private Error|Exception|null $exception = null;

  /**
   * @var bool
   */
  private bool $exceptionEnabled = true;

  /**
   * @return bool
   */
  public function isExceptionEnabled(): bool
  {
    return $this->exceptionEnabled;
  }

  /**
   * @param bool $exceptionEnabled
   */
  public function setExceptionEnabled(bool $exceptionEnabled): void
  {
    $this->exceptionEnabled = $exceptionEnabled;
  }

  /**
   * @return void
   */
  public function init(): void
  {
    parent::init();

    $this->getResponse()->setStatusCode(
      $this->getException()->getCode()
    );

    $this->getResponse()->setStatusMessage(
      $this->getException()->getMessage()
    );
  }

  /**
   * @return Exception|Error|null
   */
  public function getException(): Exception|Error|null
  {
    return $this->exception;
  }

  /**
   * @param $exception
   * @return void
   */
  public function setException($exception): void
  {
    $this->exception = $exception;
  }
}
