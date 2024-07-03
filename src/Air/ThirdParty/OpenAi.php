<?php

declare(strict_types=1);

namespace Air\ThirdParty;

use Air\Core\Front;
use Air\Http\Request;
use Exception;

class OpenAi
{
  /**
   * @var string
   */
  private string $key;

  /**
   * @var array
   */
  private array $models;

  /**
   * @var array
   */
  private array $messages = [];

  /**
   * @param array{key: string, models: array{chat: string, image: string}}|null $openAiConfig
   * @throws Core\Exception\ClassWasNotFound
   */
  public function __construct(?array $openAiConfig = [])
  {
    if (!$openAiConfig) {
      $openAiConfig = Front::getInstance()->getConfig()['air']['openai'];
    }

    $this->key = $openAiConfig['key'];
    $this->models = $openAiConfig['models'];
  }

  /**
   * @param string $question
   * @param bool|null $json
   * @return string
   * @throws Exception
   */
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
      'model' => $this->models['chat'],
      'messages' => $this->messages,
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

  /**
   * @param string $prompt
   * @param int|null $width
   * @param int|null $height
   * @return string
   * @throws Exception
   */
  public function image(string $prompt, ?int $width = 1024, ?int $height = 1024): string
  {
    $body = [
      'model' => $this->models['image'],
      'prompt' => $prompt,
      'n' => 1,
      'size' => $width . 'x' . $height
    ];

    $answer = (new Request())
      ->url('https://api.openai.com/v1/images/generations')
      ->method(Request::POST)
      ->type('json')
      ->bearer($this->key)
      ->timeout(30000)
      ->body($body)
      ->do()
      ->body;

    if (isset($answer['error'])) {
      throw new Exception('OpenAi error:' . $answer['error']['type'] . '. Message: ' . $answer['error']['message']);
    }

    return $answer['data'][0]['url'];
  }

  /**
   * @param string $question
   * @param bool|null $json
   * @return mixed
   * @throws Exception
   */
  public static function ask(string $question, ?bool $json = false): mixed
  {
    return (new self())->message($question, $json);
  }

  /**
   * @param string $prompt
   * @param int|null $width
   * @param int|null $height
   * @return string
   * @throws Exception
   */
  public function img(string $prompt, ?int $width = 1024, ?int $height = 1024): string
  {
    return (new self())->image($prompt, $width, $height);
  }
}