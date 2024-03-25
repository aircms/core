<?php

declare(strict_types=1);

namespace Air;

use Air\Core\Exception\ClassWasNotFound;
use Air\Core\Front;

class Fcm
{
  /**
   * @var string
   */
  private string $url = 'https://fcm.googleapis.com/fcm/send';

  /**
   * @var string|null
   */
  private ?string $apiKey = null;

  /**
   * @var array
   */
  private array $tokens = [];

  /**
   * @var string|null
   */
  private ?string $title = null;

  /**
   * @var string|null
   */
  private ?string $body = null;

  /**
   * @var string|null
   */
  private ?string $icon = null;

  /**
   * @var string|null
   */
  private ?string $clickAction = null;

  /**
   * @var string
   */
  private string $payloadType = 'home';

  /**
   * @var string|null
   */
  private ?string $payloadData = null;

  /**
   * @param array $options
   * @throws ClassWasNotFound
   */
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

  /**
   * @param string $token
   * @return Fcm
   */
  public function addToken(string $token): Fcm
  {
    $this->tokens[] = $token;
    return $this;
  }

  /**
   * @return bool|string
   */
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

  /**
   * @return string
   */
  public function getUrl(): string
  {
    return $this->url;
  }

  /**
   * @param string $url
   * @return Fcm
   */
  public function setUrl(string $url): Fcm
  {
    $this->url = $url;
    return $this;
  }

  /**
   * @return string
   */
  public function getApiKey(): string
  {
    return $this->apiKey;
  }

  /**
   * @param string $apiKey
   * @return Fcm
   */
  public function setApiKey(string $apiKey): Fcm
  {
    $this->apiKey = $apiKey;
    return $this;
  }

  /**
   * @return string|null
   */
  public function getIcon(): ?string
  {
    return $this->icon;
  }

  /**
   * @param string $icon
   * @return Fcm
   */
  public function setIcon(string $icon): Fcm
  {
    $this->icon = $icon;
    return $this;
  }

  /**
   * @return array
   */
  public function getTokens(): array
  {
    return $this->tokens;
  }

  /**
   * @param array $tokens
   * @return Fcm
   */
  public function setTokens(array $tokens): Fcm
  {
    $this->tokens = $tokens;
    return $this;
  }

  /**
   * @return string
   */
  public function getTitle(): string
  {
    return $this->title;
  }

  /**
   * @param string $title
   * @return Fcm
   */
  public function setTitle(string $title): Fcm
  {
    $this->title = $title;
    return $this;
  }

  /**
   * @return string
   */
  public function getBody(): string
  {
    return $this->body;
  }

  /**
   * @param string $body
   * @return Fcm
   */
  public function setBody(string $body): Fcm
  {
    $this->body = $body;
    return $this;
  }

  /**
   * @return string
   */
  public function getClickAction(): string
  {
    return $this->clickAction;
  }

  /**
   * @param string $clickAction
   * @return Fcm
   */
  public function setClickAction(string $clickAction): Fcm
  {
    $this->clickAction = $clickAction;
    return $this;
  }

  /**
   * @return string|null
   */
  public function getPayloadType(): ?string
  {
    return $this->payloadType;
  }

  /**
   * @param string $payloadType
   */
  public function setPayloadType(string $payloadType): void
  {
    $this->payloadType = $payloadType;
  }

  /**
   * @return string
   */
  public function getPayloadData(): string
  {
    return $this->payloadData ?? '';
  }

  /**
   * @param string $payloadData
   */
  public function setPayloadData(string $payloadData): void
  {
    $this->payloadData = $payloadData;
  }
}
