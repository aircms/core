<?php

declare(strict_types=1);

namespace Air\ThirdParty\GoogleOAuth;

use Air\Type\TypeAbstract;

class Profile extends TypeAbstract
{
  /**
   * @var string|null
   */
  public ?string $email = null;

  /**
   * @var string|null
   */
  public ?string $firstName = null;

  /**
   * @var string|null
   */
  public ?string $secondName = null;

  /**
   * @var string|null
   */
  public ?string $image = null;

  /**
   * @return string|null
   */
  public function getEmail(): ?string
  {
    return $this->email;
  }

  /**
   * @param string|null $email
   */
  public function setEmail(?string $email): void
  {
    $this->email = $email;
  }

  /**
   * @return string|null
   */
  public function getFirstName(): ?string
  {
    return $this->firstName;
  }

  /**
   * @param string|null $firstName
   */
  public function setFirstName(?string $firstName): void
  {
    $this->firstName = $firstName;
  }

  /**
   * @return string|null
   */
  public function getSecondName(): ?string
  {
    return $this->secondName;
  }

  /**
   * @param string|null $secondName
   */
  public function setSecondName(?string $secondName): void
  {
    $this->secondName = $secondName;
  }

  /**
   * @return string|null
   */
  public function getImage(): ?string
  {
    return $this->image;
  }

  /**
   * @param string|null $image
   */
  public function setImage(?string $image): void
  {
    $this->image = $image;
  }
}