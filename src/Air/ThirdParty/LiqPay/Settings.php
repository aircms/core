<?php

declare(strict_types=1);

namespace Air\ThirdParty\LiqPay;

use Air\Crud\Model\Language;
use Air\Type\TypeAbstract;

class Settings extends TypeAbstract
{
  public string $currency = 'UAH';
  public float $amount = 0;
  public mixed $orderId = null;
  public string $serverUrl = '';
  public ?Language $language = null;
  public string $description = '';
  public string $action = 'pay';
  public string $version = '3';

  public function toLiqPayData(): array
  {
    $langKey = trim(strtolower($this->language?->key ?? ''));
    if (!strlen($langKey) || $langKey === 'ua') {
      $langKey = 'uk';
    }

    $data = [
      ...parent::toArray(),
      ...[
        'order_id' => $this->orderId,
        'language' => $langKey,
        'server_url' => $this->serverUrl,
      ]
    ];

    unset($data['orderId']);
    unset($data['serverUrl']);

    return $data;
  }
}