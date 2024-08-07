<?php

declare(strict_types=1);

namespace Air\ThirdParty;

use Air\Http\Request;
use Exception;
use Throwable;

class NewPost
{
  /**
   * @var string|null
   */
  protected ?string $key = null;

  /**
   * @param string $key
   * @return bool
   * @throws Exception
   */
  public static function isKeyValid(string $key): bool
  {
    try {
      $self = new self($key);
      return !!count($self->warehouses('Одесса', 1));
    } catch (Throwable) {
    }

    return false;
  }

  /**
   * @param string $key
   * @throws Exception
   */
  public function __construct(string $key)
  {
    if (!strlen($key)) {
      throw new Exception('New Post API-key is empty');
    }

    $this->key = $key;
  }

  /**
   * @param string $city
   * @param int $limit
   * @param int $offset
   * @return array
   * @throws Exception
   */
  public function warehouses(string $city, int $limit = 20, int $offset = 0): array
  {
    $data = [
      'apiKey' => $this->key,
      'modelName' => 'AddressGeneral',
      'calledMethod' => 'getWarehouses',
      'methodProperties' => [
        'CityName' => $city,
        'Limit' => $limit,
        'Offset' => $offset
      ]
    ];

    var_dump($data);

    $newPost = Request::postJson('https://api.novaposhta.ua/v2.0/json/', $data);

    if (!$newPost->isOk()) {
      $exceptionData = [
        'request' => $data,
        'headers' => $newPost->header,
        'body' => $newPost->body
      ];
      throw new Exception(json_encode($exceptionData, JSON_PRETTY_PRINT));
    }

    $warehouses = [];
    foreach ($newPost->body['data'] as $warehouse) {
      // if ($warehouse['TypeOfWarehouse'] === '9a68df70-0267-42a8-bb5c-37f427e36ee4') {}
      $warehouses[] = $warehouse['Description'];
    }
    return $warehouses;
  }
}