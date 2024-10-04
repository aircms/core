<?php

declare(strict_types=1);

namespace Air;

class Mail
{
  /**
   * @var string
   */
  private string $charSet;

  /**
   * @var string
   */
  private string $encryption;

  /**
   * @var int
   */
  private int $port;

  /**
   * @var string
   */
  private string $host;

  /**
   * @var string
   */
  private string $username;

  /**
   * @var string
   */
  private string $password;

  /**
   * @var string
   */
  private string $fromName;

  /**
   * @var string
   */
  private string $fromEmail;

  /**
   * @return string
   */
  public function getCharSet(): string
  {
    return $this->charSet;
  }

  /**
   * @param string $charSet
   */
  public function setCharSet(string $charSet): void
  {
    $this->charSet = $charSet;
  }

  /**
   * @return string
   */
  public function getEncryption(): string
  {
    return $this->encryption;
  }

  /**
   * @param string $encryption
   */
  public function setEncryption(string $encryption): void
  {
    $this->encryption = $encryption;
  }

  /**
   * @return int
   */
  public function getPort(): int
  {
    return $this->port;
  }

  /**
   * @param int $port
   */
  public function setPort(int $port): void
  {
    $this->port = $port;
  }

  /**
   * @return string
   */
  public function getHost(): string
  {
    return $this->host;
  }

  /**
   * @param string $host
   */
  public function setHost(string $host): void
  {
    $this->host = $host;
  }

  /**
   * @return string
   */
  public function getUsername(): string
  {
    return $this->username;
  }

  /**
   * @param string $username
   */
  public function setUsername(string $username): void
  {
    $this->username = $username;
  }

  /**
   * @return string
   */
  public function getPassword(): string
  {
    return $this->password;
  }

  /**
   * @param string $password
   */
  public function setPassword(string $password): void
  {
    $this->password = $password;
  }

  /**
   * @return string
   */
  public function getFromName(): string
  {
    return $this->fromName;
  }

  /**
   * @param string $fromName
   */
  public function setFromName(string $fromName): void
  {
    $this->fromName = $fromName;
  }

  /**
   * @return string
   */
  public function getFromEmail(): string
  {
    return $this->fromEmail;
  }

  /**
   * @param string $fromEmail
   */
  public function setFromEmail(string $fromEmail): void
  {
    $this->fromEmail = $fromEmail;
  }

  /**
   * @param string $charSet
   * @param string $encryption
   * @param int $port
   * @param string $host
   * @param string $username
   * @param string $password
   * @param string $fromName
   * @param string $fromEmail
   */
  public function __construct(
    string $charSet,
    string $encryption,
    int    $port,
    string $host,
    string $username,
    string $password,
    string $fromName,
    string $fromEmail
  )
  {
    $this->charSet = $charSet;
    $this->encryption = $encryption;
    $this->port = $port;
    $this->host = $host;
    $this->username = $username;
    $this->password = $password;
    $this->fromName = $fromName;
    $this->fromEmail = $fromEmail;
  }

  /**
   * @param string $email
   * @param string $subject
   * @param string $body
   * @param string $name
   * @param array $vars
   * @return bool
   */
  public function send(
    string $email,
    string $subject,
    string $body,
    string $name = '',
    array  $vars = []
  ): bool
  {
    if (count($vars)) {
      foreach ($vars as $key => $value) {
        $subject = str_replace('{{' . $key . '}}', $value, $subject);
        $body = str_replace('{{' . $key . '}}', $value, $body);
      }
    }

    return true;

//    $mail = new PHPMailer(true);
//    $mail->SMTPDebug = 0;
//    $mail->isSMTP();
//    $mail->CharSet = $this->charSet;
//    $mail->SMTPAuth = true;
//    $mail->SMTPSecure = $this->encryption;
//    $mail->Host = $this->host;
//    $mail->Username = $this->username;
//    $mail->Password = $this->password;
//    $mail->Port = $this->port;
//
//    $mail->setFrom($this->fromEmail, $this->fromName);
//    $mail->addAddress($email, $name);
//
//    $mail->isHTML();
//    $mail->Subject = $subject;
//    $mail->Body = $body;
//    $mail->AltBody = strip_tags($body);
//
//    return $mail->send();
  }
}