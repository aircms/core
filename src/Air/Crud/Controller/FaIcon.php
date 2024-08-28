<?php

declare(strict_types=1);

namespace Air\Crud\Controller;

use Air\Core\Exception\ClassWasNotFound;
use Air\Core\Exception\DomainMustBeProvided;
use Air\Core\Exception\RouterVarMustBeProvided;
use Air\Paginator;
use Exception;

class FaIcon extends Multiple
{
  /**
   * @var string
   */
  public string $search = '';

  /**
   * @var string
   */
  public string $style = \Air\Type\FaIcon::STYLE_SOLID;

  /**
   * @return void
   * @throws ClassWasNotFound
   * @throws DomainMustBeProvided
   * @throws RouterVarMustBeProvided
   */
  public function init(): void
  {
    parent::init();

    $filter = $this->getParam('filter', []) ?? [];

    $this->search = $filter['search'] ?? '';
    $this->style = $filter['style'] ?? \Air\Type\FaIcon::STYLE_SOLID;
  }

  /**
   * @return array[]
   */
  protected function getFilterWithValues(): array
  {
    return [
      ['type' => 'search', 'by' => ['search'], 'value' => $this->search],
      [
        'type' => 'select',
        'by' => 'style',
        'allowAll' => false,
        'value' => $this->style,
        'options' => [
          ['value' => \Air\Type\FaIcon::STYLE_SOLID, 'title' => 'Solid'],
          ['value' => \Air\Type\FaIcon::STYLE_REGULAR, 'title' => 'Regular'],
          ['value' => \Air\Type\FaIcon::STYLE_LIGHT, 'title' => 'Light'],
          ['value' => \Air\Type\FaIcon::STYLE_THIN, 'title' => 'Thin'],
          ['value' => \Air\Type\FaIcon::STYLE_DUOTONE, 'title' => 'Doutone'],
          ['value' => \Air\Type\FaIcon::STYLE_SHARP_SOLID, 'title' => 'Sharp slid'],
          ['value' => \Air\Type\FaIcon::STYLE_SHARP_REGULAR, 'title' => 'Sharp regular'],
          ['value' => \Air\Type\FaIcon::STYLE_SHARP_LIGHT, 'title' => 'Sharp light'],
          ['value' => \Air\Type\FaIcon::STYLE_SHARP_THIN, 'title' => 'Sharp thin'],
          ['value' => \Air\Type\FaIcon::STYLE_SHARP_DUOTONE, 'title' => 'Sharp duotone'],
        ]
      ]
    ];
  }

  /**
   * @return void
   * @throws Exception
   */
  public function select(): void
  {
    $paginator = new Paginator(
      page: (int)$this->getParam('page', 1),
      itemsPerPage: 30,
      items: \Air\Type\FaIcon::getIcons(),
      filter: function (mixed $index, string $icon) {
        if (strlen($this->search)) {
          return str_contains(strtolower($icon), trim(strtolower($this->search)));
        }
        return true;
      }
    );
    $this->getView()->setVars([
      'isSelectControl' => true,
      'style' => $this->style,
      'paginator' => $paginator,
      'filter' => $this->getFilterWithValues()
    ]);

    $this->getView()->setScript('fa-icon/index');
  }
}
