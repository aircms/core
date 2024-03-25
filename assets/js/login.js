$(document).ready(() => {
  $('.fade').addClass('show');

  $('form').on('submit', function () {
    loader.show();
    $.post('/_auth', $(this).serialize())
      .done(() => window.location = '/')
      .fail(() => {
        $('[data-error]').removeClass('d-none');
        loader.hide();
      });
    return false;
  });
});