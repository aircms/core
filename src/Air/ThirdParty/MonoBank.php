<?php

declare(strict_types=1);

namespace Air\ThirdParty;

use Air\Http\Request;
use Air\Log;
use Exception;
use Throwable;

class MonoBank
{
  protected ?string $key = null;

  public static function isValidKey(string $key): bool
  {
    try {
      $self = new self($key);
      return !!$self->createInvoice(5, 'http://airshop.com/', 'http://airshop.com/webhook');
    } catch (Throwable) {
    }
    return false;
  }

  public function __construct(string $key)
  {
    $this->key = $key;
  }

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