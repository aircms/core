<?php

declare(strict_types=1);

namespace Air\Form\Element;

class Meta extends ElementAbstract
{
  /**
   * @var string|null
   */
  public ?string $elementTemplate = 'form/element/meta';

  /**
   * @return \Air\Type\Meta|null
   */
  public function getValue(): ?\Air\Type\Meta
  {
    $value = (array)parent::getValue();

    if (isset($value['ogImage']) && is_string($value['ogImage'])) {
      $value['ogImage'] = json_decode($value['ogImage'], true);
    }

    $value['useModelData'] = isset($value['useModelData']) && $value['useModelData'];

    return new \Air\Type\Meta($value);
  }
}