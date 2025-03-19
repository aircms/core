<?php

declare(strict_types=1);

namespace Air\Crud\Controller;

use Air\Core\Front;
use Air\Crud\Locale;
use Air\Form\Element\Text;
use Air\Form\Element\Tiny;
use Air\Form\Form;
use Air\Form\Generator;

/**
 * @mod-manageable true
 *
 * @mod-controls {"type": "copy"}
 *
 * @mod-header {"title": "URL", "by": "url"}
 * @mod-header {"title": "Subject", "by": "subject"}
 * @mod-header {"title": "Language", "by": "language", "type": "model", "field": "title"}
 */
class EmailTemplate extends Multiple
{
  protected function getTitle(): string
  {
    return Locale::t('Email / Templates');
  }

  protected function getModelClassName(): string
  {
    return \Air\Crud\Model\EmailTemplate::class;
  }

  protected function getAdminMenuItem(): array|null
  {
    return ['icon' => 'page'];
  }

  protected function getEntity(): string
  {
    return Front::getInstance()->getConfig()['air']['admin']['emailTemplates'];
  }

  protected function getForm($model = null): Form
  {
    /** @var \Air\Crud\Model\EmailTemplate $model */

    return Generator::full($model, [
      'General' => [
        new Text('subject'),
        new Tiny('body')
      ],
    ]);
  }
}