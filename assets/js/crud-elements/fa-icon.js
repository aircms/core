$(document).ready(() => {

  $(document).on('click', '[data-admin-faicon] [data-admin-faicon-iframe]', function () {
    const container = $(this).closest('[data-admin-faicon]');
    modal.model($(this).data('admin-faicon-iframe'), function (icon) {

      container.find('[data-admin-faicon-input]').val(JSON.stringify({
        icon: icon.icon,
        style: icon.style
      }));

      container.find('[data-admin-faicon-value]').html(
        '<i class="fa-' + icon.icon + ' ' + icon.style + ' me-3 fs-5"></i> ' + icon.title
      );

      modal.hide();
    });
  });

  $(document).on('dblclick', '[data-admin-faicon-id]', function () {
    window.parent.postMessage({
      row: {
        icon: $(this).data('admin-faicon-id'),
        title: $(this).data('admin-faicon-title'),
        style: $('[data-admin-select-input]').val()
      }
    }, "*");
  });

  $(document).on('click', '[data-admin-faicon-style] [data-admin-select-option]', function () {
    const style = $(this).data('value');
    $('[data-admin-faicon-search] .icon').each(function () {
      if (!$(this).hasClass('fa-brands')) {
        $(this)
          .removeClass('fa-solid')
          .removeClass('fa-regular')
          .removeClass('fa-light')
          .removeClass('fa-thin')
          .removeClass('fa-duotone')
          .removeClass('fa-sharp')
          .removeClass('fa-sharp-duotone')
          .addClass(style)
      }
    });
  });

  $(document).on('input', '[data-admin-faicon-search-input]', function () {
    const val = $(this).val();
    const container = $(this).closest('[data-admin-faicon-choose-container]');

    container.find('[data-admin-faicon-search]').each(function () {
      if ($(this).data('admin-faicon-search').toString().search(val) === -1) {
        $(this).addClass('d-none');
      } else {
        $(this).removeClass('d-none');
      }
    });
  });
});