let currentScriptDir = document.currentScript.src.split('?')[0].split('/');
currentScriptDir.pop();
currentScriptDir = currentScriptDir.join('/');

$(document).ready(() => {
  let activeTab = 0;
  wait.on('[data-admin-tab]', (tab) => new Tab(tab, {
    active: activeTab,
    change: (index) => activeTab = index
  }));

  $(document).on('submit', '[data-admin-from-manage]', (e) => {
    $.post(location.pathname, $(e.currentTarget).serialize())
      .done((response) => {

        if (typeof response === 'string') {
          $(nav.layoutSelector).html(response);
          return;
        }

        if (response.quickSave) {
          if (response.newOne) {
            nav.nav(response.url);
          }
          nav.reload();
        } else {
          nav.nav(response.url);
        }
        notify.success('Saved!');
      })
      .fail((e) => {
        $(nav.layoutSelector).html(e.responseText);
        notify.show('Error! Check input values.', {style: 'danger', dismissDelay: 2000});
      });
    return false;
  });

  const injectQuickSave = () => {
    const form = $('[data-admin-from-manage]');
    const quickSave = form.find('input[name="quick-save"]');
    quickSave.val('1');
    setTimeout(() => quickSave.val(''), 500);
  };

  $(document).on('click', '[data-admin-from-manage-save]', () => {
    const form = $('[data-admin-from-manage]');
    if (form.length) {
      form.submit();
    }
  });

  $(document).on('keydown', (e) => {
    const form = $('[data-admin-from-manage]');
    if (form.length && e.key === 's' && e.ctrlKey) {
      injectQuickSave();
      form.submit();
      e.preventDefault();
    }
  });
});