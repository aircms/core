<?php

declare(strict_types=1);

namespace Air\ThirdParty;

use Air\Core\Exception\ClassWasNotFound;
use Air\Http\Request;
use Air\Log;
use Air\Model\Exception\CallUndefinedMethod;
use Air\Model\Exception\ConfigWasNotProvided;
use Air\Model\Exception\DriverClassDoesNotExists;
use Air\Model\Exception\DriverClassDoesNotExtendsFromDriverAbstract;
use Exception;
use Throwable;

class MonoBank
{
  /**
   * @var string|null
   */
  protected ?string $key = null;

  /**
   * @param string $key
   * @return bool
   */
  public static function isValidKey(string $key): bool
  {
    try {
      $self = new self($key);
      return !!$self->createInvoice(5, 'http://airshop.com/', 'http://airshop.com/webhook');
    } catch (Throwable) {
    }
    return false;
  }

  /**
   * @param string $key
   */
  public function __construct(string $key)
  {
    $this->key = $key;
  }

  /**
   * @param float $amount
   * @param string $redirectUrl
   * @param string $webHookUrl
   * @return array
   * @throws ClassWasNotFound
   * @throws CallUndefinedMethod
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   * @throws Exception
   */
  public function createInvoice(float $amount, string $redirectUrl, string $webHookUrl): array
  {
    $data = [
      "amount" => $amount * 100,
      "redirectUrl" => $redirectUrl,
      "webHookUrl" => $webHookUrl,
    ];

    $mono = (new Request())
      ->url('https://api.monobank.ua/api/merchant/invoice/create')
      ->headers(['X-Token' => $this->key])
      ->method(Request::POST)
      ->type('json')
      ->body($data)
      ->do();

    if (!$mono->isOk()) {
      $exceptionData = [
        'request' => $data,
        'response' => $mono->body
      ];
      throw new Exception(json_encode($exceptionData, JSON_PRETTY_PRINT));
    }
    return $mono->body;
  }

  /**
   * @param string $invoiceId
   * @return array
   * @throws ClassWasNotFound
   * @throws CallUndefinedMethod
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   */
  public function invoiceStatus(string $invoiceId): array
  {
    $mono = (new Request())
      ->url('https://api.monobank.ua/api/merchant/invoice/status')
      ->headers(['X-Token' => $this->key])
      ->get(['invoiceId' => $invoiceId])
      ->do();

    if (!$mono->isOk()) {
      Log::error('Error getting invoice status by invoiceId . ' . $invoiceId, [
        'invoiceId' => $invoiceId,
        'response' => $mono->body
      ]);
      throw new Exception('Error getting invoice status by invoiceId . ' . $invoiceId);
    }
    return $mono->body;
  }
}