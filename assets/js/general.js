$(document).ready(() => {
  $(document).on('click', '[data-admin-clean-cache]', function () {
    if ($(this).data('confirm').length) {
      modal.question($(this).data('confirm')).then(() => {
        loader.show();
        $.get($(this).data('href'), () => loader.hide());
      });
    }
  });
});