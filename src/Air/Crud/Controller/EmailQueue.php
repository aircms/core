<?php

declare(strict_types=1);

namespace Air\Crud\Controller;

use Air\Core\Front;
use Air\Crud\Locale;
use Air\Email;

/**
 * @mod-sorting {"when": -1}
 *
 * @mod-filter {"type": "search", "by": ["toAddress", "toName", "subject", "body", "status"]}
 */
class EmailQueue extends Multiple
{
  protected function getTitle(): string
  {
    return Locale::t('Email / Queue');
  }

  protected function getModelClassName(): string
  {
    return \Air\Crud\Model\EmailQueue::class;
  }

  protected function getAdminMenuItem(): array|null
  {
    return ['icon' => 'database'];
  }

  protected function getEntity(): string
  {
    return Front::getInstance()->getConfig()['air']['admin']['emailQueue'];
  }

  public function manage(string $id = null): void
  {
    $email = \Air\Crud\Model\EmailQueue::fetchOne(['id' => $id]);

    $this->getView()->assign('email', $email);
    $this->getView()->assign('entity', $this->getEntity());

    $this->getView()->setScript('emailQueue/manage');
  }

  public function body(string $id): string
  {
    $this->getView()->setAutoRender(false);
    $this->getView()->setLayoutEnabled(false);

    return \Air\Crud\Model\EmailQueue::fetchOne(['id' => $id])->body;
  }

  public function getHeader(): array
  {
    return [
      'status' => [
        'title' => 'Status',
        'source' => function (\Air\Crud\Model\EmailQueue $emailQueue) {
          return self::multiple([
            self::badge(date('Y-m-d H:i', $emailQueue->when), self::DARK),
            match ($emailQueue->status) {
              \Air\Crud\Model\EmailQueue::STATUS_NEW => self::badge('Planned', self::WARNING),
              \Air\Crud\Model\EmailQueue::STATUS_SUCCESS => self::badge('Success', self::SUCCESS),
              \Air\Crud\Model\EmailQueue::STATUS_FAIL => self::badge('Fail', self::DANGER),
            }
          ]);
        }
      ],
      'destination' => [
        'title' => 'Destination',
        'source' => function (\Air\Crud\Model\EmailQueue $emailQueue) {
          return self::multiple([
            self::badge($emailQueue->toName, self::INFO),
            self::badge($emailQueue->toAddress, self::INFO),
          ]);
        }
      ],
      'subject' => [
        'title' => 'Subject',
        'type' => 'text'
      ],
      'body' => [
        'title' => 'Body',
        'type' => 'longtext'
      ],
    ];
  }

  public function send(string $id): array
  {
    return ['success' => Email::send(
      \Air\Crud\Model\EmailQueue::one(['id' => $id])
    )];
  }

  public function clear(): void
  {
    \Air\Crud\Model\EmailQueue::remove([
      'status' => ['$ne' => \Air\Crud\Model\EmailQueue::STATUS_NEW]
    ]);

    $this->redirect('/' . $this->getEntity());
  }

  public function clearAllForce(): void
  {
    \Air\Crud\Model\EmailQueue::remove();
    $this->redirect('/' . $this->getEntity());
  }

  protected function getHeaderButtons(): array
  {
    return [[
      'title' => 'Delete all successful ones?',
      'url' => ['controller' => $this->getEntity(), 'action' => 'clear'],
      'confirm' => 'Are you sure want to remove all successful emails?',
      'style' => ['icon' => 'xmark', 'color' => 'danger']
    ], [
      'title' => 'Clear all Emails?',
      'url' => ['controller' => $this->getEntity(), 'action' => 'clear'],
      'confirm' => 'Are you sure want to remove all emails?',
      'style' => ['icon' => 'xmark', 'color' => 'danger']
    ]];
  }

  protected function getControls(): array
  {
    return [[
      'title' => 'Details',
      'url' => ['controller' => $this->getEntity(), 'action' => 'manage'],
      'icon' => 'file-page',
    ]];
  }
}