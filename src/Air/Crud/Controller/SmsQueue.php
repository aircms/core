<?php

declare(strict_types=1);

namespace Air\Crud\Controller;

use Air\Core\Front;
use Air\Crud\Controller\MultipleHelper\Accessor\Control;
use Air\Crud\Controller\MultipleHelper\Accessor\Header;
use Air\Crud\Controller\MultipleHelper\Accessor\HeaderButton;
use Air\Crud\Controller\MultipleHelper\Accessor\Ui;
use Air\Sms;
use Air\Type\FaIcon;

/**
 * @mod-sorting {"when": -1}
 *
 * @mod-filter {"type": "search", "by": ["toAddress", "message", "status"]}
 */
class SmsQueue extends Multiple
{
  protected function getTitle(): string
  {
    return 'Sms / Queue';
  }

  protected function getModelClassName(): string
  {
    return \Air\Crud\Model\SmsQueue::class;
  }

  protected function getIcon(): string
  {
    return FaIcon::ICON_DATABASE;
  }

  protected function getEntity(): string
  {
    return Front::getInstance()->getConfig()['air']['admin']['smsQueue'];
  }

  public function manage(string $id = null): void
  {
    $sms = \Air\Crud\Model\SmsQueue::fetchOne(['id' => $id]);

    $this->getView()->assign('sms', $sms);
    $this->getView()->assign('entity', $this->getEntity());

    $this->getView()->setScript('smsQueue/manage');
  }

  public function body(string $id): string
  {
    $this->getView()->setAutoRender(false);
    $this->getView()->setLayoutEnabled(false);

    return \Air\Crud\Model\SmsQueue::fetchOne(['id' => $id])->body;
  }

  public function getHeader(): array
  {
    return [
      Header::source('Status', function (\Air\Crud\Model\SmsQueue $sms) {
        return Ui::multiple([
          Ui::badge(date('Y-m-d H:i', $sms->when), Ui::DARK),
          match ($sms->status) {
            \Air\Crud\Model\SmsQueue::STATUS_NEW => Ui::badge('Planned', Ui::WARNING),
            \Air\Crud\Model\SmsQueue::STATUS_SUCCESS => Ui::badge('Success', Ui::SUCCESS),
            \Air\Crud\Model\SmsQueue::STATUS_FAIL => Ui::badge('Fail', Ui::DANGER),
          }
        ]);
      }),
      Header::source('Destination', function (\Air\Crud\Model\SmsQueue $sms) {
        return Ui::badge($sms->toAddress, Ui::INFO);
      }),
      Header::longtext(by: 'message')
    ];
  }

  public function send(string $id): array
  {
    return ['success' => Sms::send(
      \Air\Crud\Model\SmsQueue::one(['id' => $id])
    )];
  }

  public function clear(): void
  {
    \Air\Crud\Model\SmsQueue::batchRemove([
      'status' => ['$ne' => \Air\Crud\Model\SmsQueue::STATUS_NEW]
    ]);

    $this->redirect('/' . $this->getEntity());
  }

  public function clearAllForce(): void
  {
    \Air\Crud\Model\SmsQueue::batchRemove();
    $this->redirect('/' . $this->getEntity());
  }

  protected function getHeaderButtons(): array
  {
    return [
      HeaderButton::item(
        title: 'Delete all successful ones?',
        url: ['controller' => $this->getEntity(), 'action' => 'clear'],
        confirm: 'Are you sure want to remove all successful sms?',
        style: Ui::DANGER,
        icon: FaIcon::ICON_XMARK
      ),
      HeaderButton::item(
        title: 'Clear all SMS?',
        url: ['controller' => $this->getEntity(), 'action' => 'clearAllForce'],
        confirm: 'Are you sure want to remove all successful sms?',
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
