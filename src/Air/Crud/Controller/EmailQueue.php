<?php

declare(strict_types=1);

namespace Air\Crud\Controller;

use Air\Core\Front;
use Air\Crud\Controller\MultipleHelper\Accessor\Control;
use Air\Crud\Controller\MultipleHelper\Accessor\Header;
use Air\Crud\Controller\MultipleHelper\Accessor\HeaderButton;
use Air\Crud\Controller\MultipleHelper\Accessor\Ui;
use Air\Email;
use Air\Type\FaIcon;

/**
 * @mod-sorting {"when": -1}
 *
 * @mod-filter {"type": "search", "by": ["toAddress", "toName", "subject", "body", "status"]}
 */
class EmailQueue extends Multiple
{
  protected function getTitle(): string
  {
    return 'Email / Queue';
  }

  protected function getModelClassName(): string
  {
    return \Air\Crud\Model\EmailQueue::class;
  }

  protected function getIcon(): string
  {
    return FaIcon::ICON_DATABASE;
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
      Header::source('Status', function (\Air\Crud\Model\EmailQueue $emailQueue) {
        return Ui::multiple([
          Ui::badge(date('Y-m-d H:i', $emailQueue->when), Ui::DARK),
          match ($emailQueue->status) {
            \Air\Crud\Model\EmailQueue::STATUS_NEW => Ui::badge('Planned', Ui::WARNING),
            \Air\Crud\Model\EmailQueue::STATUS_SUCCESS => Ui::badge('Success', Ui::SUCCESS),
            \Air\Crud\Model\EmailQueue::STATUS_FAIL => Ui::badge('Fail', Ui::DANGER),
          }
        ]);
      }),
      Header::source('Destination', function (\Air\Crud\Model\EmailQueue $emailQueue) {
        return Ui::multiple([
          Ui::badge($emailQueue->toName, Ui::INFO),
          Ui::badge($emailQueue->toAddress, Ui::INFO),
        ]);
      }),
      Header::text(by: 'subject'),
      Header::longtext(by: 'body')
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
    return [
      HeaderButton::item(
        title: 'Delete all successful ones?',
        url: ['controller' => $this->getEntity(), 'action' => 'clear'],
        confirm: 'Are you sure want to remove all successful emails?',
        style: Ui::DANGER,
        icon: FaIcon::ICON_XMARK
      ),
      HeaderButton::item(
        title: 'Clear all Emails?',
        url: ['controller' => $this->getEntity(), 'action' => 'clearAllForce'],
        confirm: 'Are you sure want to remove all successful emails?',
        style: Ui::DANGER,
        icon: FaIcon::ICON_XMARK
      ),
    ];
  }

  protected function getControls(): array
  {
    return [
      Control::item(
        title: 'Details',
        url: ['controller' => $this->getEntity(), 'action' => 'manage'],
        icon: FaIcon::ICON_PAGE
      )
    ];
  }
}