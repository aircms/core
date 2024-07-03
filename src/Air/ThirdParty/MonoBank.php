<?php

declare(strict_types=1);

namespace Air\ThirdParty;

use Air\Http\Request;
use Air\Log;
use Exception;

class MonoBank
{
  /**
   * @var string|null
   */
  protected ?string $key = null;

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
   * @throws \Air\Core\Exception\ClassWasNotFound
   * @throws \Air\Model\Exception\CallUndefinedMethod
   * @throws \Air\Model\Exception\ConfigWasNotProvided
   * @throws \Air\Model\Exception\DriverClassDoesNotExists
   * @throws \Air\Model\Exception\DriverClassDoesNotExtendsFromDriverAbstract
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
      Log::error('Error creating invoice', [
        'request' => $data,
        'response' => $mono->body
      ]);
      throw new Exception('Error creating invoice');
    }
    return $mono->body;
  }

  /**
   * @param string $invoiceId
   * @return array
   * @throws \Air\Core\Exception\ClassWasNotFound
   * @throws \Air\Model\Exception\CallUndefinedMethod
   * @throws \Air\Model\Exception\ConfigWasNotProvided
   * @throws \Air\Model\Exception\DriverClassDoesNotExists
   * @throws \Air\Model\Exception\DriverClassDoesNotExtendsFromDriverAbstract
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
    return $results->body;
  }
}