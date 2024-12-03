<?php

declare(strict_types=1);

namespace Air\Type;

class Quote extends TypeAbstract
{
  /**
   * @var string
   */
  public string $author = '';

  /**
   * @var string
   */
  public string $quote = '';

  /**
   * @return string
   */
  public function getAuthor(): string
  {
    return $this->author;
  }

  /**
   * @param string $author
   * @return void
   */
  public function setAuthor(string $author): void
  {
    $this->author = $author;
  }

  /**
   * @return string
   */
  public function getQuote(): string
  {
    return $this->quote;
  }

  /**
   * @param string $quote
   * @return void
   */
  public function setQuote(string $quote): void
  {
    $this->quote = $quote;
  }
}