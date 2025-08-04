<?php

declare(strict_types=1);

namespace Air\Form;

use Air\Crud\Locale;
use Air\Form\Element\ElementAbstract;
use Air\Form\Element\Hidden;
use Air\Form\Element\Tab;
use Air\Form\Exception\Validation;
use Air\Model\ModelAbstract;
use Air\View\View;

class Form
{
  public array $elements = [];
  public string $firstTabLabel = '';
  public string $submit = 'Save';
  public string $template = 'index.phtml';
  public ?View $view = null;
  public string $method = 'POST';
  public ?string $action = null;
  public mixed $data = [];
  public ?string $returnUrl = null;

  public function getElements(): array
  {
    return $this->elements;
  }

  public function getFirstTabLabel(): string
  {
    return $this->firstTabLabel;
  }

  public function getGroupedElements(): array
  {
    $groupTitle = $this->getFirstTabLabel();

    foreach ($this->getElements() as $element) {
      if ($element instanceof Tab) {
        $groupTitle = $element->getName();
        break;
      }
    }

    $elements = [];

    foreach ($this->getElements() as $element) {
      if ($element instanceof Tab) {
        $groupTitle = $element->getName();
        continue;
      }
      $elements[$groupTitle] = $elements[$groupTitle] ?? ['hasErrors' => false, 'elements' => []];
      $elements[$groupTitle]['elements'][] = $element;

      if ($element->hasError()) {
        $elements[$groupTitle]['hasErrors'] = true;
      }
    }

    return $elements;
  }

  public function getElement(string $name): ElementAbstract
  {
    return $this->elements[$name];
  }

  public function addElements(array $elements): void
  {
    if (count($elements)) {

      $firstKey = array_keys($elements)[0];

      if (is_string($firstKey)) {

        foreach ($elements as $separator => $groupElements) {

          $this->addElement(new Tab(Locale::t($separator)));

          foreach ($groupElements as $element) {
            $this->elements[$element->getName()] = $element;
          }
        }
      } else {
        foreach ($elements as $element) {
          $this->elements[$element->getName()] = $element;
        }
      }
    }
  }

  public function addElement(ElementAbstract $element): void
  {
    $this->elements[$element->getName()] = $element;
  }

  public function getValues(): array
  {
    $values = [];
    foreach ($this->getElements() as $name => $element) {
      if (get_class($element) != 'Air\Form\Element\Tab') {
        $values[$name] = $element->getValue();
      }
    }
    return $values;
  }

  public function getCleanValues(): array
  {
    $values = [];
    foreach ($this->getElements() as $name => $element) {
      if (get_class($element) != 'Air\Form\Element\Tab') {
        $values[$name] = $element->getCleanValue();
      }
    }
    return $values;
  }

  public function getSubmit(): string
  {
    return $this->submit;
  }

  public function setSubmit(string $submit): void
  {
    $this->submit = $submit;
  }

  public function getTemplate(): string
  {
    return $this->template;
  }

  public function setTemplate(string $template): void
  {
    $this->template = $template;
  }

  public function getView(): View
  {
    return $this->view;
  }

  public function setView(View $view): void
  {
    $this->view = $view;
  }

  public function getMethod(): string
  {
    return $this->method;
  }

  public function setMethod(string $method): void
  {
    $this->method = $method;
  }

  public function getAction(): ?string
  {
    return $this->action;
  }

  public function setAction(string $action): void
  {
    $this->action = $action;
  }

  public function getData(): array
  {
    return $this->data;
  }

  public function setData(mixed $data): void
  {
    $this->data = $data;
  }

  public function getReturnUrl(): string
  {
    return $this->returnUrl;
  }

  public function setReturnUrl(string $returnUrl): void
  {
    $this->returnUrl = $returnUrl;
  }

  public function isValid(array $data = []): bool
  {
    $isValid = true;

    foreach ($this->getElements() as $name => $element) {

      if (get_class($element) != 'Air\Form\Element\Tab') {

        if (!$element->isValid($data[$name] ?? null)) {
          $isValid = false;
        }
      }
    }

    return $isValid;
  }

  public static function validated(array $data = []): array
  {
    return (new static())->validateOfFail($data);
  }

  public function validateOfFail(array $data = []): array
  {
    if (!$this->isValid($data)) {
      throw new Validation($this);
    }
    return $this->getValues();
  }

  public function __construct(array $options = [], array $elements = [])
  {
    $this->firstTabLabel = 'General';
    foreach ($options as $name => $value) {
      if (is_callable([$this, 'set' . ucfirst($name)])) {
        call_user_func_array([$this, 'set' . ucfirst($name)], [$value]);
      }
    }
    $this->init($this->data ?? [], $elements);
  }

  public function __toString(): string
  {
    if (!$this->view) {
      $this->view = new View();

      $this->view->setPath(realpath(__DIR__ . '/Form'));
      $this->view->setScript('index');
    }

    $this->view->assign('form', $this);

    return $this->view->render();
  }

  public function init($model = null, array $elements = []): void
  {
    if ($model && is_subclass_of($model, ModelAbstract::class)) {

      $this->addElement(
        new Hidden('id', [
          'value' => $model->id,
          'allowNull' => true
        ])
      );
    }

    $this->addElements($elements);

    foreach ($this->getElements() as $element) {
      if (!($element instanceof Tab)) {
        $element->setValue($model[$element->getName()] ?? null);
      }
      $element->init();
    }
  }

  public function getErrorMessages(): array
  {
    $errorMessages = [];
    foreach ($this->getElements() as $element) {
      if ($elementErrorMessages = $element->getErrorMessages()) {
        $errorMessages[$element->getName()] = $elementErrorMessages;
      }
    }
    return $errorMessages;
  }

  public function getErrorFields(): array
  {
    return array_keys($this->getErrorMessages());
  }

  public static function inputs(mixed $data = null, array $elements = []): static
  {
    return new static(['data' => $data], $elements);
  }
}
