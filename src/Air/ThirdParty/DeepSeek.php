<?php

declare(strict_types=1);

namespace Air\ThirdParty;

use Air\Core\Front;
use Air\Http\Request;
use Exception;

class DeepSeek
{
  private array $messages = [];

  public function __construct(
    private ?string $key = null,
    private ?string $model = null,
  )
  {
    if (!$this->key || !$this->model) {
      $settings = \Air\Crud\Model\OpenAi::one();
      $this->key = $settings->key;
      $this->model = $settings->model;
    }
  }

  public function addMessage(string $role, string $content): void
  {
    $this->messages[] = [
      'role' => $role,
      'content' => $content
    ];
  }

  public function message(string $question, ?bool $json = false): mixed
  {
    if ($json) {
      $this->messages[] = [
        'role' => 'system',
        'content' => 'You are a helpful assistant designed to output JSON.'
      ];
    }

    $this->messages[] = [
      'role' => 'user',
      'content' => $question
    ];

    $body = [
      'model' => $this->model,
      'messages' => $this->messages,
      'stream' => false
    ];

    if ($json) {
      $body['response_format'] = ['type' => 'json_object'];
    }

    $answer = (new Request())
      ->url('https://api.openai.com/v1/chat/completions')
      ->method(Request::POST)
      ->type('json')
      ->bearer($this->key)
      ->timeout(30000)
      ->body($body)
      ->do()
      ->body;

    $message = $answer['choices'][0]['message'] ?? false;

    if (!$message) {
      throw new Exception('OpenAi error:' . $answer['error']['type'] . '. Message: ' . $answer['error']['message']);
    }

    $this->messages[] = $message;

    if ($json) {
      return json_decode($message['content'], true);
    }

    return $message['content'];
  }

  public static function ask(string $question, ?bool $json = false): mixed
  {
    return (new static())->message($question, $json);
  }
}