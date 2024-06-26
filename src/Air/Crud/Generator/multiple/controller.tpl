<?php

declare(strict_types=1);

namespace {namespace};

use Air\Crud\Controller\Multiple;

/**
 * @mod-title {title}
 * @mod-manageable true
 * @mod-sortable title
 * @mod-sorting {"createdAt": -1}
 * @mod-items-per-page 10
 *
 * @mod-header-button {"title": "URL Array", "url": {"action": "index"}}
 * @mod-header-button {"title": "URL String", "url": "https://google.com"}
 * @mod-header-button {"title": "Confirm", "url": {}, "confirm": "Are you sure?"}
 * @mod-header-button {"title": "Icon", "url": {}, "style": {"icon": "star"}}
 * @mod-header-button {"title": "Color", "url": {}, "style": {"color": "info"}}
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
 * @mod-export {"title": "Title", "by": "title"}
 * @mod-export {"title": "Description", "by": "description"}
 *
 * @mod-header {"title": "Image", "by": "image", "type": "image"}
 * @mod-header {"title": "Title", "by": "title", "sorting": true}
 * @mod-header {"title": "Related", "by": "singleModelRef", "type": "model", "field": "title"}
 * @mod-header {"title": "Language", "by": "language", "type": "model", "field": "title"}
 * @mod-header {"title": "Created", "by": "createdAt", "type": "dateTime", "sorting": true}
 * @mod-header {"title": "Enabled", "type": "bool", "by": "enabled"}
 *
 * @mod-filter {"type": "search", "by": ["title", "subTitle", "description", "content"]}
 * @mod-filter {"type": "bool", "by": "enabled", "true": "Enabled", "false": "Disabled", "value": "true"}
 * @mod-filter {"type": "select", "by": "status", "options":[{"value": "status-1", "title": "First status"}, {"value": "status-2", "title": "Second status"}]}
 * @mod-filter {"type": "model", "by": "singleModelRef", "field": "title", "model": "\\App\\Model\\{name}"}
 * @mod-filter {"type": "model", "by": "language", "field": "title", "model": "\\Air\\Crud\\Model\\Language"}
 * @mod-filter {"type": "dateTime", "by": "createdAt"}
 */
class {name} extends Multiple
{
}
