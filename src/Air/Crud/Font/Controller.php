<?php

declare(strict_types=1);

namespace Air\Crud\Font;

use Air\Core\Exception\ClassWasNotFound;
use Air\Core\Front;
use Air\Crud\Controller\Multiple;
use Air\Form\Element\Storage;
use Air\Form\Form;
use Air\Form\Generator;
use Air\Map;
use Air\Model\Exception\CallUndefinedMethod;
use Air\Model\Exception\ConfigWasNotProvided;
use Air\Model\Exception\DriverClassDoesNotExists;
use Air\Model\Exception\DriverClassDoesNotExtendsFromDriverAbstract;

/**
 * @mod-title Fonts
 * @mod-manageable true
 *
 * @mod-header {"title": "Title", "by": "title"}
 * @mod-header {"title": "Activity", "type": "bool", "by": "enabled"}
 */
class Controller extends Multiple
{
  /**
   * @return string
   */
  public function getModelClassName(): string
  {
    return Model::class;
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
   * @param Model $model
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
    $this->getResponse()->setHeader('Content-type', 'text/css');
    return $this->getView()->render('fonts', ['fonts' => Model::all()]);
  }

  /**
   * @return string
   * @throws CallUndefinedMethod
   * @throws ClassWasNotFound
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   */
  public function fonts(): string
  {
    $fonts = [];
    foreach (Model::all() as $font) {
      $fonts[] = $font->title . '=' . $font->title;
    }

    return implode('; ', $fonts);
  }
}
