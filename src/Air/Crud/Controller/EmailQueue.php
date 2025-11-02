<?php

declare(strict_types=1);

namespace Air\Crud\Controller;

use Air\Crud\Controller\MultipleHelper\Accessor\Control;
use Air\Crud\Controller\MultipleHelper\Accessor\Header;
use Air\Crud\Controller\MultipleHelper\Accessor\HeaderButton;
use Air\Crud\Controller\MultipleHelper\Accessor\Ui;
use Air\Crud\Locale;
use Air\Crud\Nav;
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
    return Locale::t('Email / Queue');
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
    return Nav::getSettingsItem(Nav::SETTINGS_EMAIL_QUEUE)['alias'];
  }

  /**
   * @param \Air\Crud\Model\EmailQueue $model
   * @return null
   */
  protected function getForm($model = null): null
  {
    return null;
  }

  public function details(\Air\Crud\Model\EmailQueue $id): string
  {
    $this->getView()->assign('email', $id);
    $this->getView()->assign('entity', $this->getEntity());

    return $this->getView()->render('emailQueue/details');
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
          Ui::badge(Ui::date($emailQueue->when), Ui::LIGHT),
          Ui::badge(Ui::date($emailQueue->when), Ui::DARK),
          match ($emailQueue->status) {
            \Air\Crud\Model\EmailQueue::STATUS_NEW => Ui::badge(Locale::t('Planned'), Ui::WARNING),
            \Air\Crud\Model\EmailQueue::STATUS_SUCCESS => Ui::badge(Locale::t('Success'), Ui::SUCCESS),
            \Air\Crud\Model\EmailQueue::STATUS_FAIL => Ui::badge(Locale::t('Fail'), Ui::DANGER),
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

  public function send(\Air\Crud\Model\EmailQueue $id): array
  {
    return ['success' => Email::send($id)];
  }

  public function clear(): void
  {
    \Air\Crud\Model\EmailQueue::batchRemove([
      'status' => ['$ne' => \Air\Crud\Model\EmailQueue::STATUS_NEW]
    ]);

    $this->redirect('/' . $this->getEntity());
  }

  public function clearAllForce(): void
  {
    \Air\Crud\Model\EmailQueue::batchRemove();
    $this->redirect('/' . $this->getEntity());
  }

  protected function getHeaderButtons(): array
  {
    return [
      HeaderButton::item(
        title: Locale::t('Delete all successful ones?'),
        url: ['controller' => $this->getEntity(), 'action' => 'clear'],
        confirm: Locale::t('Are you sure want to remove all successful emails?'),
        style: Ui::DANGER,
        icon: FaIcon::ICON_XMARK
      ),
      HeaderButton::item(
        title: Locale::t('Clear all Emails?'),
        url: ['controller' => $this->getEntity(), 'action' => 'clearAllForce'],
        confirm: Locale::t('Are you sure want to remove all successful emails?'),
        style: Ui::DANGER,
        icon: FaIcon::ICON_XMARK
      ),
    ];
  }

  protected function getControls(): array
  {
    return [
      Control::html(
        title: Locale::t('Details'),
        url: ['controller' => $this->getEntity(), 'action' => 'details'],
        icon: FaIcon::ICON_PAGE
      )
    ];
  }
}