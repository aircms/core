$(document).ready(() => {
  let lastActiveTab = 0;
  wait.on('[data-admin-tab]', (tab) => {
    new Tab(tab, {
      active: lastActiveTab, change: (index) => lastActiveTab = index
    })
  });

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
        notify.success(locale('Saved!'));
      })
      .fail((e) => {
        $(nav.layoutSelector).html(e.responseText);
        notify.show(locale('Error! Check input values.'), {style: 'danger', dismissDelay: 2000});
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

  $(document).on('click', '[data-admin-from-manage-back]', () => {
    if (nav.getQueryParams()?.returnUrl) {
      nav.nav(decodeURIComponent(nav.getQueryParams().returnUrl));
      return;
    }

    if (nav.history.length < 2) {
      nav.nav('/' + location.pathname.replaceAll('/', ' ').trim().split(' ')[0]);
    } else {
      let url = '';
      try {
        url = nav.history[nav.history.length - 2];
      } catch {
        url = nav.history[nav.history.length - 1];
      }

      if (url.search('/manage') === -1) {
        nav.nav(url);
      } else {
        nav.nav('/' + location.pathname.replaceAll('/', ' ').trim().split(' ')[0]);
      }
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

  $(document).on('click', '[data-admin-manage-switch-language] [data-admin-select-option]', function () {
    const languageId = $(this).data('value');
    const map = JSON.parse($(this).closest('[data-admin-manage-switch-language]').find('[data-admin-manage-switch-language-map]').text().trim());

    try {
      const recordId = map.find(m => m.languageId === languageId).recordId;
      const controller = map.find(m => m.languageId === languageId).controller;
      const isQuickManage = map.find(m => m.languageId === languageId).isQuickManage;

      let url = '/' + controller + '/manage?id=' + recordId;
      if (isQuickManage) {
        url += '&isQuickManage=1';
      }
      nav.nav(url);
    } catch {
    }
  });
});