<?php

declare(strict_types=1);

namespace Air\Crud\Controller;

use Air\Core\Controller;
use Air\Core\Exception\ClassWasNotFound;
use Air\Model\Exception\CallUndefinedMethod;
use Air\Model\Exception\ConfigWasNotProvided;
use Air\Model\Exception\DriverClassDoesNotExists;
use Air\Model\Exception\DriverClassDoesNotExtendsFromDriverAbstract;

class FontsUi extends Controller
{
  /**
   * @return string
   * @throws ClassWasNotFound
   * @throws CallUndefinedMethod
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   */
  public function index(): string
  {
    $this->getView()->setPath(realpath(__DIR__ . '/../View'));
    $this->getResponse()->setHeader('Content-type', 'text/css');

    $css = [];
    foreach (\Air\Crud\Model\Font::all() as $font) {
      $css[] = $font->asCss();
    }
    return implode('', $css);
  }
}
