$(document).ready(() => {

  $(document).on('keydown', '[name="filter[search]"]', function (e) {
    if (e.keyCode === 13) {
      $('[data-admin-table-form]').submit();
    }
  });

  $(document).on('click', '[data-admin-table-form] [data-search-button]', () => $('[data-admin-table-form]').submit());

  $(document).on('click', '[data-admin-table-row-select]', (e) => {
    const row = JSON.parse($(e.currentTarget).find('[data-admin-table-row-object]').html());
    window.parent.postMessage({row: row}, "*");
  });

  $(document).on('keydown', (e) => {
    const searchInput = $('[name="filter[search]"]');
    if (searchInput.length && e.key === 'f' && e.ctrlKey) {
      e.preventDefault();
      searchInput.focus();
      searchInput[0].setSelectionRange(0, $(searchInput).val().length);
    }
  });

  $(document).on('click', '[data-admin-table-row-copy]', function () {
    modal.question('Copy record?').then(() => {
      $.post($(this).data('admin-table-row-copy'))
        .done(() => {
          nav.reload();
          notify.success(locale('Yeah! Record has been copied.'));
        })
        .fail(() => notify.danger(locale('Something went wrong. Can not copy record')));
    });
  });

  $(document).on('click', '[data-admin-table-row-activity]', function () {
    modal.question($(this).data('confirm')).then(() => {
      $.post($(this).data('admin-table-row-activity'))
        .done(() => {
          nav.reload();
          notify.success(locale('Yeah! Visibility has been changed'));
        })
        .fail(() => notify.danger(locale('Something went wrong. Can not set visibility')));
    });
  });

  $(document).on('click', '[data-admin-table-paginator] [data-page]', function () {
    $('[data-admin-table-form] [name="page"]').val($(this).data('page'));
    $('[data-admin-table-form]').submit();
  });

  $(document).on('submit', '[data-admin-table-form]', function () {
    nav.nav(location.pathname + '?' + $(this).serialize());
    return false;
  });
});