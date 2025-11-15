<?php

declare(strict_types=1);

namespace Air\Crud\Controller;

use Air\Core\Front;
use Air\Crud\Nav;
use Air\Form\Form;
use Air\Form\Input;
use Air\Type\FaIcon;

class Billing extends Single
{
  protected function getTitle(): string
  {
    return 'Billing';
  }

  public function getModelClassName(): string
  {
    return \Air\Crud\Model\Billing::class;
  }

  protected function getIcon(): string
  {
    return FaIcon::ICON_MONEY_BILL_1;
  }

  protected function getEntity(): string
  {
    return Nav::getSettingsItem(Nav::SETTINGS_BILLING)['alias'];
  }

  /**
   * @param \Air\Crud\Model\Billing $model
   * @return Form
   */
  protected function getForm($model = null): Form
  {
    return new Form(['data' => $model], [
      'LiqPay' => [
        Input::checkbox('liqPayEnabled'),
        Input::text('liqPayPublicKey', allowNull: true),
        Input::text('liqPayPrivateKey', allowNull: true),

        Input::checkbox('liqPaySandboxEnabled'),
        Input::text('liqPaySandboxPublicKey', allowNull: true),
        Input::text('liqPaySandboxPrivateKey', allowNull: true),
      ],
      'MonoPay' => [
        Input::checkbox('monoPayEnabled'),
        Input::text('monoPayKey', allowNull: true),

        Input::checkbox('monoPaySandboxEnabled'),
        Input::text('monoPaySandboxKey', allowNull: true),
      ]
    ]);
  }
}
