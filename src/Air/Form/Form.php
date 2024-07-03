<?php

declare(strict_types=1);

namespace Air\Form;

use Air\Crud\Locale;
use Air\Form\Element\ElementAbstract;
use Air\Form\Element\Hidden;
use Air\Form\Element\Tab;
use Air\Form\Exception\FilterClassWasNotFound;
use Air\Form\Exception\ValidatorClassWasNotFound;
use Air\Model\ModelAbstract;
use Air\View\View;

class Form
{
  /**
   * @var ElementAbstract[]
   */
  public array $elements = [];

  /**
   * @var string
   */
  public string $firstTabLabel = '';

  /**
   * @var string
   */
  public string $submit = 'Save';

  /**
   * @var string
   */
  public string $template = 'index.phtml';

  /**
   * @var View|null
   */
  public ?View $view = null;

  /**
   * @var string
   */
  public string $method = 'POST';

  /**
   * @var string|null
   */
  public ?string $action = null;

  /**
   * @var mixed
   */
  public mixed $data;

  /**
   * @var string|null
   */
  public ?string $returnUrl = null;

  /**
   * @return ElementAbstract[]
   */
  public function getElements(): array
  {
    return $this->elements;
  }

  /**
   * @return string
   */
  public function getFirstTabLabel(): string
  {
    return $this->firstTabLabel;
  }

  /**
   * @return ElementAbstract[][]
   */
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

  /**
   * @param string $name
   * @return ElementAbstract
   */
  public function getElement(string $name): ElementAbstract
  {
    return $this->elements[$name];
  }

  /**
   * @param ElementAbstract[] $elements
   */
  public function addElements(array $elements): void
  {
    if (count($elements)) {

      $firstKey = array_keys($elements)[0];

      if (is_string($firstKey)) {

        foreach ($elements as $separator => $groupElements) {

          $this->addElement(new Tab($separator));

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

  /**
   * @param ElementAbstract $element
   */
  public function addElement(ElementAbstract $element): void
  {
    $this->elements[$element->getName()] = $element;
  }

  /**
   * @return array
   */
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

  /**
   * @return array
   */
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

  /**
   * @return string
   */
  public function getSubmit(): string
  {
    return $this->submit;
  }

  /**
   * @param string $submit
   */
  public function setSubmit(string $submit): void
  {
    $this->submit = $submit;
  }

  /**
   * @return string
   */
  public function getTemplate(): string
  {
    return $this->template;
  }

  /**
   * @param string $template
   */
  public function setTemplate(string $template): void
  {
    $this->template = $template;
  }

  /**
   * @return View
   */
  public function getView(): View
  {
    return $this->view;
  }

  /**
   * @param View $view
   */
  public function setView(View $view): void
  {
    $this->view = $view;
  }

  /**
   * @return string
   */
  public function getMethod(): string
  {
    return $this->method;
  }

  /**
   * @param string $method
   */
  public function setMethod(string $method): void
  {
    $this->method = $method;
  }

  /**
   * @return string|null
   */
  public function getAction(): ?string
  {
    return $this->action;
  }

  /**
   * @param string $action
   */
  public function setAction(string $action): void
  {
    $this->action = $action;
  }

  /**
   * @return array|ModelAbstract[]
   */
  public function getData(): array
  {
    return $this->data;
  }

  /**
   * @param mixed $data
   * @return void
   */
  public function setData(mixed $data): void
  {
    $this->data = $data;
  }

  /**
   * @return string
   */
  public function getReturnUrl(): string
  {
    return $this->returnUrl;
  }

  /**
   * @param string $returnUrl
   */
  public function setReturnUrl(string $returnUrl): void
  {
    $this->returnUrl = $returnUrl;
  }

  /**
   * @param array $data
   * @return bool
   * @throws FilterClassWasNotFound
   * @throws ValidatorClassWasNotFound
   */
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

  /**
   * @param array $options
   * @param array $elements
   * @throws \Air\Core\Exception\ClassWasNotFound
   */
  public function __construct(array $options = [], array $elements = [])
  {
    $this->firstTabLabel = Locale::t('General');

    foreach ($options as $name => $value) {

      if (is_callable([$this, 'set' . ucfirst($name)])) {
        call_user_func_array([$this, 'set' . ucfirst($name)], [$value]);
      }
    }

    $this->init($this->data, $elements);
  }

  /**
   * @return string
   * @throws \Exception
   */
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

  /**
   * @param null $model
   * @param ElementAbstract[]|null $elements
   */
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
        $element->setValue($model[$element->getName()]);
      }
      $element->init();
    }
  }

  /**
   * @return array
   */
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
}
