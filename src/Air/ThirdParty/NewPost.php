<?php

declare(strict_types=1);

namespace Air\ThirdParty;

use Air\Http\Request;
use Air\Log;
use Exception;

class NewPost
{
  /**
   * @var string|null
   */
  protected ?string $key = null;

  /**
   * @param string $key
   */
  public function __construct(string $key)
  {
    $this->key = $key;
  }

  /**
   * @param string $city
   * @return array
   * @throws Exception
   */
  public function warehouses(string $city): array
  {
    $data = [
      'apiKey' => $this->key,
      'modelName' => 'AddressGeneral',
      'calledMethod' => 'getWarehouses',
      'methodProperties' => [
        'CityName' => $city
      ]
    ];

    $newPost = Request::postJson('https://api.novaposhta.ua/v2.0/json/', $data);

    if (!$newPost->isOk()) {
      Log::error('Error getting warehouses for ' . $city, [
        'request' => $data,
        'response' => $newPost->body
      ]);
    }

    $warehouses = [];
    foreach ($newPost->body['data'] as $warehouse) {
      // if ($warehouse['TypeOfWarehouse'] === '9a68df70-0267-42a8-bb5c-37f427e36ee4') {}
      $warehouses[] = $warehouse['Description'];
    }
    return $warehouses;
  }
}