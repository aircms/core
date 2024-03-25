$(document).ready(() => {
  wait.on('[data-admin-select]', (select) => {

    const title = $(select).find('[data-selected]').length ?
      $(select).find('[data-selected]').html() :
      $(select).find('[data-admin-select-option]').first().html();

    $(select).find('[data-admin-select-title]').html(title.trim());

    if ($(select).find('[data-admin-select-search]').length) {
      $(select).find('[data-admin-select-search]').on('input', function () {
        const search = $(this).val().trim().toLocaleLowerCase();
        if (!search.length) {
          return;
        }
        $(this).closest('[data-admin-select]').find('[data-admin-select-option][data-value]').each(function () {
          if (!$(this).html().trim().toLocaleLowerCase().includes(search)) {
            $(this).closest('li').addClass('d-none');
          } else {
            $(this).closest('li').removeClass('d-none');
          }
        });
      });

      $(select).find('[data-admin-select-search]').on('blur', function () {
        setTimeout(() => {
          $(this).val('');
          $(this).closest('[data-admin-select]').find('[data-admin-select-option][data-value]').each(function () {
            $(this).closest('li').removeClass('d-none');
          });
        }, 100);
      });
    }

    $(select).find('[data-admin-select-option]').on('click', function () {
      const select = $(this).closest('[data-admin-select]');

      select.find('[data-admin-select-title]').html($(this).html().trim());
      select.find('[data-admin-select-input]').val($(this).data('value'));

      if (select.data('admin-select-submit') === true) {
        setTimeout(() => select.closest('form').submit(), 200);
      }
    });
  });
});