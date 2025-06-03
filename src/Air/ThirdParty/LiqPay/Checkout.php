<?php

declare(strict_types=1);

namespace Air\ThirdParty\LiqPay;

use Air\Type\TypeAbstract;

class Checkout extends TypeAbstract
{
  public string $data = '';
  public string $signature = '';
  public mixed $order = null;
}