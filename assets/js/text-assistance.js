const getAccessoryValue = (accessoryItem) => {
  const selector = '[data-target-accessory-id="' + accessoryItem + '"]';
  let value = null;
  if ($(selector).closest('[data-admin-form-rich-content-element-container]').length) {
    value = $(selector).find('textarea').val();
  }
  if (!value) {
    value = $(selector).find('input, textarea').val();
  }
  return value;
};

const getAccessoryInput = (accessoryItem) => {
  const selector = '[data-target-accessory-id="' + accessoryItem + '"]';
  let value = null;
  if ($(selector).closest('[data-admin-form-rich-content-element-container]').length) {
    value = $(selector).find('textarea');
  }
  if (!value) {
    value = $(selector).find('input, textarea');
  }
  return value;
};

const applyAccessoryValue = (accessoryItem, value) => {
  const selector = '[data-target-accessory-id="' + accessoryItem + '"]';
  if ($(selector).data('admin-form-tiny') !== undefined) {
    $(selector).find('textarea').val(value);
    $(selector).find('[data-admin-form-tiny-preview]').html(value);
    return;
  }
  $(selector).find('input, textarea').val(value);
};

$(document).ready(() => {

  const listContainerOpen = '<ul style="z-index: 2000" class="dropdown-menu shadow-5-strong overflow-hidden" data-admin-contextmenu-target="{id}">';
  const listContainerClose = '</ul>';

  const listButtonGoogleTranslate = `<li><a class="dropdown-item" role="button" data-accessory-google-translate="{id}"><i class="fas fa-language me-2"></i>Google translate</a></li>`;
  const listButtonDeepl = `<li><a class="dropdown-item" role="button" data-accessory-deepl="{id}"><i class="fas fa-language me-2"></i>Deepl</a></li>`;
  const listButtonDeepSeek = `<li><a class="dropdown-item" role="button" data-accessory-deep-seek="{id}"><i class="fas fa-robot me-2"></i>Deep seek</a></li>`;

  const appendAccessory = (container) => {
    if ((!window.googleTranslate && !window.deepl && !window.deepSeek) || $(container).data('target-accessory-id')) {
      return;
    }
    const contextmenuId = 'assistance' + Math.random().toString();
    $(container).attr('data-admin-contextmenu', contextmenuId);
    $(container).attr('data-target-accessory-id', contextmenuId);
    let contextMenu = listContainerOpen.replaceAll('{id}', contextmenuId);

    if (window.googleTranslate) {
      contextMenu += listButtonGoogleTranslate.replaceAll('{id}', contextmenuId);
    }
    if (window.deepl) {
      contextMenu += listButtonDeepl.replaceAll('{id}', contextmenuId);
    }
    if (window.deepSeek) {
      contextMenu += listButtonDeepSeek.replaceAll('{id}', contextmenuId);
    }
    contextMenu += listContainerClose;
    $(container).append(contextMenu);

    new ContextMenu(container);
  };

  wait.on('.form-outline', (el) => {
    const input = $(el).find('input, textarea');
    if (input.attr('type') !== 'search' && $(input).attr('data-admin-datetimepicker') === undefined && $(input).attr('name') !== 'url' && !$(input).closest('.dropdown-menu').length) {
      appendAccessory(el);
    }
  });

  wait.on('[data-admin-form-tiny]', (el) => {
    appendAccessory(el);
  });
});