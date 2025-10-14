<?php

declare(strict_types=1);

namespace Air\ThirdParty\Payment;

use Air\Http\Request;
use Exception;
use InvalidArgumentException;

class LiqPay extends Payment
{
  private string $publicKey;
  private string $privateKey;

  public function __construct(array $settings)
  {
    parent::__construct($settings);

    if (!isset($this->settings['publicKey']) || !isset($this->settings['privateKey'])) {
      throw new InvalidArgumentException("LiqPay settings are missing");
    }

    $this->publicKey = $this->settings['publicKey'];
    $this->privateKey = $this->settings['privateKey'];
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
      'version' => 3,
      'action' => 'pay',
      'currency' => 'UAH',
      'public_key' => $this->publicKey,
      'amount' => $amount,
      'description' => $description,
      'result_url' => $redirect,
      'server_url' => $callback,
      'order_id' => $orderId
    ];

    return new Invoice([
      'orderId' => $orderId,
      'invoiceId' => $orderId,
      'url' => "https://www.liqpay.ua/api/3/checkout?" . http_build_query($this->sign($request)),
    ]);
  }

  public function validate(array $response): false|Status
  {
    if (!isset($response['signature']) || !isset($response['data'])) {
      return false;
    }

    $signature = $response['signature'];
    $response = $response['data'];

    $requestedSignature = base64_encode(sha1($this->privateKey . $response . $this->privateKey, true));

    if ($signature !== $requestedSignature) {
      return false;
    }

    $response = json_decode(base64_decode($response), true);

    return self::dataToStatus($response);
  }

  public function status(Invoice|string $invoice): Status
  {
    $orderId = is_string($invoice) ? $invoice : $invoice->getInvoiceId();

    $request = [
      'version' => 3,
      'action' => 'status',
      'public_key' => $this->publicKey,
      'order_id' => $orderId
    ];

    $response = Request::post(
      url: "https://www.liqpay.ua/api/request",
      body: $this->sign($request)
    );

    if ($response->body['result'] === 'ok') {
      return self::dataToStatus($response->body);
    }

    throw new Exception('LiqPay status error: [order: ' . $orderId . ', err_code: ' . $response->body['err_code']);
  }

  protected function sign(array $request): array
  {
    $request = base64_encode(json_encode($request));
    $signature = base64_encode(sha1($this->privateKey . $request . $this->privateKey, true));

    return [
      'signature' => $signature,
      'data' => $request,
    ];
  }

  protected static function dataToStatus(array $data): Status
  {
    return new Status([
      'invoiceId' => $data['order_id'],
      'orderId' => $data['order_id'],
      'status' => $data['status'],
      'isPaid' => $data['status'] === 'success',
      'raw' => $data
    ]);
  }
}