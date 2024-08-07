<?php

declare(strict_types=1);

namespace Air\Form\Element;

use Air\Core\Exception\ClassWasNotFound;
use Air\Crud\Locale;
use Exception;
use Air\Filter\FilterAbstract;
use Air\Form\Exception\FilterClassWasNotFound;
use Air\Form\Exception\ValidatorClassWasNotFound;
use Air\Validator\ValidatorAbstract;
use Air\View\View;
use Throwable;

abstract class ElementAbstract
{
  /**
   * @var string|null
   */
  public ?string $name = null;

  /**
   * @var mixed|null
   */
  public mixed $value = null;

  /**
   * @var string|null
   */
  public ?string $label = null;

  /**
   * @var string|null
   */
  public ?string $description = null;

  /**
   * @var string|null
   */
  public ?string $hint = null;

  /**
   * @var FilterAbstract[]
   */
  public array $filters = [];

  /**
   * @var ValidatorAbstract[]
   */
  public array $validators = [];

  /**
   * @var bool
   */
  public bool $allowNull = false;

  /**
   * @var string[]
   */
  public array $errorMessages = [];

  /**
   * @var string
   */
  public string $containerTemplate = 'form/element/partial/container';

  /**
   * @var string
   */
  public string $errorTemplate = 'form/element/partial/error';

  /**
   * @var string
   */
  public string $labelTemplate = 'form/element/partial/label';

  /**
   * @var string|null
   */
  public ?string $elementTemplate = null;

  /**
   * @var View|null
   */
  public ?View $view = null;

  /**
   * @var string|null
   */
  public ?string $placeholder = null;

  /**
   * @var array
   */
  public array $userOptions = [];

  /**
   * ElementAbstract constructor.
   *
   * @param string $name
   * @param array $userOptions
   */
  public function __construct(string $name, array $userOptions = [])
  {
    $this->setName($name);
    foreach ($userOptions as $name => $value) {
      if (is_callable([$this, 'set' . ucfirst($name)])) {
        call_user_func_array([$this, 'set' . ucfirst($name)], [$value]);
      }
    }

    if (!$this->getLabel() &&
      $this->getElementType() !== 'hidden' &&
      $this->getElementType() !== 'tab') {
      try {
        $this->setLabel(
          ucfirst(
            strtolower(
              implode(' ',
                preg_split('/(?=[A-Z])/', $this->getName())))));
      } catch (Throwable) {
      }
    }

    $this->userOptions = $userOptions;
  }

  /**
   * @return string
   */
  public function getName(): string
  {
    return $this->name;
  }

  /**
   * @param string $name
   */
  public function setName(string $name): void
  {
    $this->name = $name;
  }

  /**
   * @return mixed|null
   */
  public function getValue(): mixed
  {
    return $this->value;
  }

  /**
   * @return mixed
   */
  public function getCleanValue(): mixed
  {
    return $this->getValue();
  }

  /**
   * @param mixed $value
   */
  public function setValue(mixed $value): void
  {
    $this->value = $value;
  }

  /**
   * @return string|null
   */
  public function getLabel(): ?string
  {
    return $this->label;
  }

  /**
   * @param string $label
   */
  public function setLabel(string $label): void
  {
    $this->label = $label;
  }

  /**
   * @return string|null
   */
  public function getDescription(): ?string
  {
    return $this->description;
  }

  /**
   * @param string $description
   */
  public function setDescription(string $description): void
  {
    $this->description = $description;
  }

  /**
   * @return string|null
   */
  public function getHint(): ?string
  {
    return $this->hint;
  }

  /**
   * @param string $hint
   */
  public function setHint(string $hint): void
  {
    $this->hint = $hint;
  }

  /**
   * @param array $validator
   */
  public function addValidator(array $validator): void
  {
    $this->validators[] = $validator;
  }

  /**
   * @return bool
   */
  public function hasError(): bool
  {
    return (bool)count($this->getErrorMessages());
  }

  /**
   * @return string[]
   */
  public function getErrorMessages(): array
  {
    return $this->errorMessages;
  }

  /**
   * @param string[] $errorMessages
   */
  public function setErrorMessages(array $errorMessages): void
  {
    $this->errorMessages = $errorMessages;
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
   * @return array
   */
  public function getUserOptions(): array
  {
    return $this->userOptions;
  }

  /**
   * @param array $userOptions
   */
  public function setUserOptions(array $userOptions): void
  {
    $this->userOptions = $userOptions;
  }

  /**
   * @return string
   */
  public function getElementType(): string
  {
    $template = explode('\\', get_called_class());
    return strtolower($template[count($template) - 1]);
  }

  /**
   * @param $value
   * @return bool
   * @throws FilterClassWasNotFound
   * @throws ValidatorClassWasNotFound
   * @throws ClassWasNotFound
   */
  public function isValid($value): bool
  {
    $this->errorMessages = [];

    foreach ($this->getValidators() as $validatorClassName => $settings) {

      if (is_int($validatorClassName)) {
        $validatorClassName = $settings;
      }

      try {
        if (!class_exists($validatorClassName)) {
          throw new ValidatorClassWasNotFound($validatorClassName);
        }
      } catch (Throwable $exception) {

        if (!($exception instanceof ValidatorClassWasNotFound)) {

          if (isset($settings['isValid'])) {

            if (!$settings['isValid']($value)) {
              $this->errorMessages[] = $settings['message'] ?? '';
            }
            continue;
          }
        }

        throw $exception;
      }

      /** @var ValidatorAbstract $validator */
      $validator = new $validatorClassName($settings['options'] ?? []);

      $validator->setAllowNull($this->isAllowNull());

      if (!$validator->isValid($value)) {
        $this->errorMessages[] = $settings['message'] ?? '';
      }
    }

    $this->value = $value;

    if (empty($value) && !$this->isAllowNull()) {
      $this->errorMessages[] = Locale::t('Could not be empty');
    }

    if (!count($this->errorMessages)) {

      foreach ($this->getFilters() as $filterClassName => $settings) {

        if (is_numeric($filterClassName)) {
          $filterClassName = $settings;
        }

        try {

          /** @var FilterAbstract $filter */
          $filter = new $filterClassName($settings['options'] ?? []);

          $this->value = $filter->filter($this->value);

        } catch (Throwable) {

          try {
            $this->value = $filterClassName($this->value);

          } catch (Exception) {
            throw new FilterClassWasNotFound($filterClassName);
          }
        }
      }
      return true;
    }
    return false;
  }

  /**
   * @return ValidatorAbstract[]
   */
  public function getValidators(): array
  {
    return $this->validators;
  }

  /**
   * @param ValidatorAbstract[] $validators
   */
  public function setValidators(array $validators): void
  {
    $this->validators = $validators;
  }

  /**
   * @return bool
   */
  public function isAllowNull(): bool
  {
    return $this->allowNull;
  }

  /**
   * @param bool $allowNull
   */
  public function setAllowNull(bool $allowNull): void
  {
    $this->allowNull = $allowNull;
  }

  /**
   * @return FilterAbstract[]
   */
  public function getFilters(): array
  {
    return $this->filters;
  }

  /**
   * @param FilterAbstract[] $filters
   */
  public function setFilters(array $filters): void
  {
    $this->filters = $filters;
  }

  /**
   * @return string
   */
  public function getErrorTemplate(): string
  {
    return $this->errorTemplate;
  }

  /**
   * @param string $errorTemplate
   */
  public function setErrorTemplate(string $errorTemplate): void
  {
    $this->errorTemplate = $errorTemplate;
  }

  /**
   * @return string
   */
  public function getLabelTemplate(): string
  {
    return $this->labelTemplate;
  }

  /**
   * @param string $labelTemplate
   */
  public function setLabelTemplate(string $labelTemplate): void
  {
    $this->labelTemplate = $labelTemplate;
  }

  /**
   * @return string
   */
  public function getElementTemplate(): string
  {
    return $this->elementTemplate;
  }

  /**
   * @param string $elementTemplate
   */
  public function setElementTemplate(string $elementTemplate): void
  {
    $this->elementTemplate = $elementTemplate;
  }

  /**
   * @return string|null
   */
  public function getPlaceholder(): ?string
  {
    return $this->placeholder;
  }

  /**
   * @param string $placeholder
   */
  public function setPlaceholder(string $placeholder): void
  {
    $this->placeholder = $placeholder;
  }

  /**
   * @return string
   * @throws Exception
   */
  public function __toString(): string
  {
    if (!$this->view) {
      $this->view = new View();
      $this->view->setPath(realpath(__DIR__ . '/../../Crud/View'));
      $this->view->setScript($this->getContainerTemplate());
    }

    $this->view->assign('element', $this);
    return $this->view->render();
  }

  /**
   * @return string
   */
  public function getContainerTemplate(): string
  {
    return $this->containerTemplate;
  }

  /**
   * @param string $containerTemplate
   */
  public function setContainerTemplate(string $containerTemplate): void
  {
    $this->containerTemplate = $containerTemplate;
  }

  public function init()
  {
  }
}
