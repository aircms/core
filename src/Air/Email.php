<?php

declare(strict_types=1);

namespace Air;

use Air\Crud\Model\EmailQueue;
use Air\Crud\Model\EmailSettings;
use Air\Crud\Model\EmailTemplate;
use Exception;
use PHPMailer\PHPMailer\PHPMailer;
use Throwable;

class Email
{
  public static function add(
    string        $toAddress,
    string        $toName,
    EmailTemplate $template,
    array         $vars = [],
    array         $attachments = null,
    bool          $force = false,
    int           $when = 0
  ): void
  {
    $email = new EmailQueue([
      'when' => $when > 0 ? $when : time(),
      'status' => EmailQueue::STATUS_NEW,
      'toAddress' => $toAddress,
      'toName' => $toName,
      'subject' => self::render($template->subject, $vars),
      'body' => self::render($template->body, $vars),
      'attachments' => $attachments ?? []
    ]);
    $email->save();
    if ($force) {
      self::send($email);
    }
  }

  protected static function render(string $text, array $vars = []): string
  {
    foreach ($vars as $k => $v) {
      $text = str_replace('{' . $k . '}', $v, $text);
    }
    return $text;
  }

  public static function send(EmailQueue $message): bool
  {
    $settings = EmailSettings::one();
    $mail = new PHPMailer(true);

    $message->debugOutput = "\n\nSettings: " . var_export($settings->getData(), true);

    try {
      $mail->SMTPDebug = 1;
      $mail->Debugoutput = function (string $debugOutput) use ($message) {
        $message->debugOutput .= "\n" . $debugOutput;
        $message->save();
      };

      $mail->isSMTP();
      $mail->CharSet = PHPMailer::CHARSET_UTF8;

      $mail->SMTPAuth = true;
      $mail->SMTPSecure = match ($settings->protocol) {
        EmailSettings::TSL => PHPMailer::ENCRYPTION_STARTTLS,
        EmailSettings::SSL => PHPMailer::ENCRYPTION_SMTPS
      };

      $mail->Host = $settings->server;
      $mail->Username = $settings->address;
      $mail->Password = $settings->password;
      $mail->Port = $settings->port;

      $mail->setFrom($settings->from, $settings->name);

      $mail->addAddress($message->toAddress, $message->toName);

      $mail->isHTML();

      $mail->Subject = $message->subject;
      $mail->Body = $message->body;
      $mail->AltBody = strip_tags($message->body);

      if (!$mail->send()) {
        throw new Exception('Email message not sent');
      }

      $message->status = EmailQueue::STATUS_SUCCESS;
      $message->save();

      return true;

    } catch (Throwable $e) {
      $message->debugOutput = $message->debugOutput . "\n" . var_export($e, true);
      $message->status = EmailQueue::STATUS_FAIL;
      $message->save();
    }

    return false;
  }

  public static function addPlain(
    string $toAddress,
    string $toName,
    string $subject,
    string $body,
    array  $vars = [],
    int    $when = 0,
    bool   $force = false,
  ): void
  {
    $email = new EmailQueue([
      'when' => $when > 0 ? $when : time(),
      'status' => EmailQueue::STATUS_NEW,
      'toAddress' => $toAddress,
      'toName' => $toName,
      'subject' => self::render($subject, $vars),
      'body' => self::render($body, $vars),
    ]);
    $email->save();
    if ($force) {
      self::send($email);
    }
  }

  public static function consume(int $chunk = 50): bool
  {
    if (!EmailSettings::one()->emailQueueEnabled) {
      return false;
    }

    $emailsQueue = EmailQueue::fetchAll(
      ['when' => ['$lte' => time()], 'status' => EmailQueue::STATUS_NEW],
      ['when' => -1],
      $chunk
    );

    foreach ($emailsQueue as $queue) {
      self::send($queue);
      unset($queue);
    }

    return true;
  }
}