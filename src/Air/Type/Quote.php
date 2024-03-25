<?php

declare(strict_types=1);

namespace Air\Type;

class Quote extends TypeAbstract
{
  public string $author = '';
  public string $quote = '';
}