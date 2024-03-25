<?php

declare(strict_types=1);

namespace Air\Form\Element;

use Exception;
use Air\Form\Exception\FilterClassWasNotFound;
use Air\Form\Exception\ValidatorClassWasNotFound;

class Page extends PageAbstract
{
  /**
   * @var bool
   */
  public static bool $templatesRendered = false;

  /**
   * @var string|null
   */
  public ?string $elementTemplate = 'form/element/page';

  /**
   * @param $value
   * @return bool
   * @throws FilterClassWasNotFound
   * @throws ValidatorClassWasNotFound
   */
  public function isValid($value): bool
  {
    $isValid = parent::isValid($value);

    if (!$isValid) {
      $this->errorMessages = ['Could not be empty'];
      return false;
    }

    if (is_string($value)) {
      $value = json_decode($value, true);
    }

    if (!count($value) && !$this->isAllowNull()) {
      $this->errorMessages = ['Could not be empty'];
      return false;
    }

    return true;
  }

  /**
   * @return \Air\Type\Page|null
   * @throws Exception
   */
  public function getValue(): ?\Air\Type\Page
  {
    $page = parent::getValue();

    if (is_string($page)) {
      $page = json_decode($page, true);
    }

    if (!$page) {
      return null;
    }

    if (!($page instanceof \Air\Type\Page)) {
      $page = new \Air\Type\Page($page);
    }

    if (!$page->getBackgroundColor()
      && !$page->getBackgroundImage()
      && !count($page->getItems())
      && $page->getWidth() === \Air\Type\Page::WIDTH
      && $page->getHeight() === \Air\Type\Page::HEIGHT
      && $page->getGutter() === \Air\Type\Page::GUTTER
    ) {
      return null;
    }
    return $page;
  }
}