<?php

declare(strict_types=1);

namespace Air\ThirdParty;

use Air\Core\Front;

class Fcm
{
  private string $url = 'https://fcm.googleapis.com/fcm/send';
  private ?string $apiKey = null;
  private array $tokens = [];
  private ?string $title = null;
  private ?string $body = null;
  private ?string $icon = null;
  private ?string $clickAction = null;
  private string $payloadType = 'home';
  private ?string $payloadData = null;

  public function __construct(array $options = [])
  {
    foreach ($options as $name => $value) {

      if (is_callable([$this, 'set' . ucfirst($name)])) {
        call_user_func_array([$this, 'set' . ucfirst($name)], [$value]);
      }
    }

    $apiKey = Front::getInstance()->getConfig()['air']['fcm']['key'] ?? false;

    if (!$this->apiKey && $apiKey) {
      $this->setApiKey($apiKey);
    }
  }

  public function addToken(string $token): Fcm
  {
    $this->tokens[] = $token;
    return $this;
  }

  public function send(): bool|string
  {
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $this->getUrl());
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

    curl_setopt($ch, CURLOPT_HTTPHEADER, [
      'Content-Type: application/json',
      'Authorization: key=' . $this->getApiKey(),
    ]);

    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
      'registration_ids' => $this->getTokens(),
      'notification' => [
        'title' => $this->getTitle(),
        'body' => $this->getBody(),
        'sound' => 'default',
      ],
      'data' => [
        'link' => [
          'type' => $this->getPayloadType(),
          'data' => $this->getPayloadData()
        ]
      ]
    ]));

    $response = curl_exec($ch);
    curl_close($ch);

    return json_decode($response, true);
  }

  public function getUrl(): string
  {
    return $this->url;
  }

  public function setUrl(string $url): Fcm
  {
    $this->url = $url;
    return $this;
  }

  public function getApiKey(): string
  {
    return $this->apiKey;
  }

  public function setApiKey(string $apiKey): Fcm
  {
    $this->apiKey = $apiKey;
    return $this;
  }

  public function getIcon(): ?string
  {
    return $this->icon;
  }

  public function setIcon(string $icon): Fcm
  {
    $this->icon = $icon;
    return $this;
  }

  public function getTokens(): array
  {
    return $this->tokens;
  }

  public function setTokens(array $tokens): Fcm
  {
    $this->tokens = $tokens;
    return $this;
  }

  public function getTitle(): string
  {
    return $this->title;
  }

  public function setTitle(string $title): Fcm
  {
    $this->title = $title;
    return $this;
  }

  public function getBody(): string
  {
    return $this->body;
  }

  public function setBody(string $body): Fcm
  {
    $this->body = $body;
    return $this;
  }

  public function getClickAction(): string
  {
    return $this->clickAction;
  }

  public function setClickAction(string $clickAction): Fcm
  {
    $this->clickAction = $clickAction;
    return $this;
  }

  public function getPayloadType(): ?string
  {
    return $this->payloadType;
  }

  public function setPayloadType(string $payloadType): void
  {
    $this->payloadType = $payloadType;
  }

  public function getPayloadData(): string
  {
    return $this->payloadData ?? '';
  }

  public function setPayloadData(string $payloadData): void
  {
    $this->payloadData = $payloadData;
  }
}
