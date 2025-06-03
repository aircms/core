<?php

declare(strict_types=1);

namespace Air\ThirdParty\LiqPay;

class LiqPay
{
  private array $data = [];
  private mixed $orderId = null;
  private bool $isPaid = false;

  public function __construct(
    private readonly string $publicKey,
    private readonly string $privateKey,
    private readonly bool   $isSandBox = false)
  {
  }

  public function getData(): array
  {
    return $this->data;
  }

  public function isSandBox(): bool
  {
    return $this->isSandBox;
  }

  public function isPaid(): bool
  {
    return $this->isPaid;
  }

  public function getOrderId(): mixed
  {
    return $this->orderId;
  }

  public function checkout(Settings $settings): Checkout
  {
    $data = $settings->toLiqPayData();
    $data['public_key'] = $this->publicKey;

    $data = base64_encode(json_encode($data));
    $signature = base64_encode(sha1($this->privateKey . $data . $this->privateKey, true));

    return new Checkout([
      'data' => $data,
      'signature' => $signature,
      'order' => $settings->orderId
    ]);
  }

  public function isValid(string $signature, string $data): bool
  {
    $requestedSignature = base64_encode(sha1($this->privateKey . $data . $this->privateKey, true));
    $isValid = $signature === $requestedSignature;

    if (!$isValid) {
      return false;
    }

    $availableStatuses = array_filter(['success', $this->isSandBox() ? 'sandbox' : null]);

    $this->data = json_decode(base64_decode($data), true);
    $this->isPaid = in_array($this->data['status'], $availableStatuses);
    $this->orderId = $this->data['order_id'];

    return true;
  }
}