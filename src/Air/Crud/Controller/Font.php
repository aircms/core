<?php

declare(strict_types=1);

namespace Air\Crud\Controller;

use Air\Core\Exception\ClassWasNotFound;
use Air\Core\Front;
use Air\Crud\Locale;
use Air\Form\Element\Storage;
use Air\Form\Form;
use Air\Form\Generator;
use Air\Map;
use Air\Model\Exception\CallUndefinedMethod;
use Air\Model\Exception\ConfigWasNotProvided;
use Air\Model\Exception\DriverClassDoesNotExists;
use Air\Model\Exception\DriverClassDoesNotExtendsFromDriverAbstract;
use Air\View\View;

/**
 * @mod-manageable true
 */
class Font extends Multiple
{
  /**
   * @return string
   * @throws ClassWasNotFound
   */
  protected function getTitle(): string
  {
    return Locale::t('Fonts');
  }

  /**
   * @return array[]
   * @throws ClassWasNotFound
   */
  protected function getHeader(): array
  {
    return [
      'title' => ['title' => Locale::t('Title'), 'by' => 'title'],
      'enabled' => ['title' => Locale::t('Activity'), 'by' => 'enabled', 'type' => 'bool'],
    ];
  }

  /**
   * @return string
   */
  public function getModelClassName(): string
  {
    return \Air\Crud\Model\Font::class;
  }

  /**
   * @return string[]
   */
  protected function getAdminMenuItem(): array
  {
    return ['icon' => 'font'];
  }

  /**
   * @return string
   * @throws ClassWasNotFound
   */
  protected function getEntity(): string
  {
    return Front::getInstance()->getConfig()['air']['admin']['fonts'];
  }

  /**
   * @param \Air\Crud\AddOn\Font\Model\Font $model
   * @return Form
   */
  protected function getForm($model = null): Form
  {
    return Generator::full($model, [
      'General' => [
        new Storage('eotIe9', [
          'value' => $model->eotIe9,
          'description' => 'IE9 Compat Modes',
          'label' => 'EOT',
          'allowNull' => true,
        ]),
        new Storage('eotIe6Ie8', [
          'value' => $model->eotIe6Ie8,
          'description' => 'Embedded opentype',
          'label' => 'EOT',
          'allowNull' => true,
        ]),
        new Storage('otf', [
          'value' => $model->otf,
          'description' => 'Opentype',
          'label' => 'OTF',
          'allowNull' => true,
        ]),
        new Storage('woff2', [
          'value' => $model->woff2,
          'description' => 'Super Modern Browsers',
          'label' => 'WOFF2',
          'allowNull' => true,
        ]),
        new Storage('woff', [
          'value' => $model->woff,
          'description' => 'Pretty Modern Browsers',
          'label' => 'WOFF',
          'allowNull' => true,
        ]),
        new Storage('ttf', [
          'value' => $model->ttf,
          'description' => 'Safari, Android, iOS',
          'label' => 'TTF',
          'allowNull' => true,
        ]),
        new Storage('svg', [
          'value' => $model->svg,
          'description' => 'Legacy iOS',
          'label' => 'SVG',
          'allowNull' => true,
        ]),
      ]
    ]);
  }

  /**
   * @return string
   * @throws ClassWasNotFound
   * @throws CallUndefinedMethod
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   */
  public function css(): string
  {
    $this->getView()->setPath(realpath(__DIR__ . '/../View'));
    $this->getResponse()->setHeader('Content-type', 'text/css');

    $css = "";
    foreach (\Air\Crud\Model\Font::all() as $font) {
      $css .= $font->asCss();
    }
    return $css;
  }

  /**
   * That kind of response need to TinyMce
   *
   * @return string
   * @throws CallUndefinedMethod
   * @throws ClassWasNotFound
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   */
  public function fonts(): string
  {
    $this->getView()->setLayoutEnabled(false);

    $fonts = [];
    foreach (\Air\Crud\Model\Font::all() as $font) {
      $fonts[] = $font->title . '=' . $font->title;
    }
    return implode('; ', $fonts);
  }
}
