<?php

declare(strict_types=1);

namespace {namespace};

use Air\Core\Exception\ClassWasNotFound;
use Air\Crud\Controller\Multiple;
use Air\Form\Form;
use Air\Form\Generator;
use Air\Model\Meta\Exception\PropertyWasNotFound;

/**
 * @mod-manageable true
 * @mod-sortable title
 * @mod-sorting {"createdAt": -1}
 * @mod-items-per-page 10
 *
 * @mod-header-button {"title": "URL Array", "url": {"action": "index"}}
 * @mod-header-button {"title": "URL String", "url": "https://google.com"}
 * @mod-header-button {"title": "Підтвердити", "url": {}, "confirm": "Ти впевнений?"}
 * @mod-header-button {"title": "Іконка", "url": {}, "style": {"icon": "star"}}
 * @mod-header-button {"title": "Колір", "url": {}, "style": {"color": "info"}}
 *
 * @mod-controls {"type": "copy"}
 * @mod-controls {"type": "separator"}
 * @mod-controls {"title": "Custom internal action", "url": {"controller": "controller", "action": "action"}, "icon": "star"}
 * @mod-controls {"title": "Custom internal action with confirmation", "url": {"controller": "controller", "action": "action"}, "icon": "star", "confirm": "Are you sure?"}
 * @mod-controls {"type": "separator"}
 * @mod-controls {"title": "Custom external url", "url": "https://domain.com/id/{id}", "icon": "star"}
 * @mod-controls {"title": "Custom external url with confirmation", "url": "https://domain.com/id/{id}", "icon": "star", "confirm": "Are you sure?"}
 * @mod-controls {"type": "separator"}
 * @mod-controls {"title": "Custom without icon", "url": {"controller": "controller"}}
 *
 * @mod-export {"title": "Назва", "by": "title"}
 * @mod-export {"title": "Опис", "by": "description"}
 *
 * @mod-header {"title": "Зобр.", "by": "image", "type": "image"}
 * @mod-header {"title": "Назва", "by": "title", "sorting": true}
 * @mod-header {"title": "Пов'язані", "by": "singleModelRef", "type": "model", "field": "title"}
 * @mod-header {"title": "Мова", "by": "language", "type": "model", "field": "title"}
 * @mod-header {"title": "Створено", "by": "createdAt", "type": "dateTime", "sorting": true}
 * @mod-header {"title": "Увімкнено", "type": "bool", "by": "enabled"}
 *
 * @mod-filter {"type": "search", "by": ["title", "subTitle", "description", "content"]}
 * @mod-filter {"type": "bool", "by": "enabled", "true": "Увімкнено", "false": "Вимкнено", "value": "true"}
 * @mod-filter {"type": "select", "title": "Статус", "by": "status", "options":[{"value": "status-1", "title": "Перший статус"}, {"value": "status-2", "title": "Другий статус"}]}
 * @mod-filter {"type": "model", "title": "{title}", "by": "singleModelRef", "field": "title", "model": "\\App\\Model\\{name}"}
 * @mod-filter {"type": "model", "title": "Мова", "by": "language", "field": "title", "model": "\\Air\\Crud\\Model\\Language"}
 * @mod-filter {"type": "dateTime", "by": "createdAt"}
 */
class {name} extends Multiple
{
  /**
   * @param \App\Model\{name} $model
   * @return Form
   * @throws ClassWasNotFound
   * @throws PropertyWasNotFound
   */
  protected function getForm($model = null): Form
  {
    return Generator::full($model, [
      'Загальні' => [

      ]
    ]);
  }
}
