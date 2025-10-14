<?php

declare(strict_types=1);

namespace Air\ThirdParty\Payment;

use Air\Http\Request;
use Exception;
use InvalidArgumentException;
use Throwable;

class MonoPay extends Payment
{
  private string $key;

  public function __construct(array $settings)
  {
    parent::__construct($settings);

    if (!isset($this->settings['key'])) {
      throw new InvalidArgumentException("MonoPay settings are missing");
    }

    $this->key = $this->settings['key'];
  }

  public function create(
    string $orderId,
    float  $amount,
    string $description,
    string $redirect,
    string $callback,
  ): Invoice
  {
    $request = [
      'amount' => $amount * 100,
      'redirectUrl' => $redirect,
      'webHookUrl' => $callback,
      'merchantPaymInfo' => [
        'reference' => $orderId,
        'destination' => $description
      ]
    ];

    $response = Request::post(
      url: 'https://api.monobank.ua/api/merchant/invoice/create',
      headers: ['X-Token' => $this->key],
      type: Request::CONTENT_TYPE_JSON,
      body: $request,
    );

    if (!$response->isOk()) {
      throw new InvalidArgumentException("MonoPay create invoice error:" . var_export($response->body, true));
    }

    return new Invoice([
      'invoiceId' => $response->body['invoiceId'],
      'url' => $response->body['pageUrl'],
      'orderId' => $orderId,
    ]);
  }

  public function validate(array $response): false|Status
  {
    try {
      return self::dataToStatus($response);
    } catch (Throwable) {
      return false;
    }
  }

  public function status(Invoice|string $invoice): Status
  {
    $invoice = is_string($invoice) ? $invoice : $invoice->getInvoiceId();

    $response = Request::fetch(
      url: "https://api.monobank.ua/api/merchant/invoice/status",
      headers: ['X-Token' => $this->key],
      get: ['invoiceId' => $invoice]
    );

    if ($response->isOk()) {
      return self::dataToStatus($response->body);
    }

    throw new Exception('MonoPay status error: [order: ' . $invoice . ', errCode: ' . $response->body['errCode']);
  }

  protected static function dataToStatus(array $data): Status
  {
    return new Status([
      'invoiceId' => $data['invoiceId'],
      'orderId' => $data['reference'],
      'status' => $data['status'],
      'isPaid' => $data['status'] === 'success',
      'raw' => $data
    ]);
  }
}