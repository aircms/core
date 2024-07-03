$(document).ready(() => {
  Embed.ready(() => {
    wait.on('[data-admin-form-embed]', (embed) => {
      new Embed(embed, {
        remove: (e) => {
          const name = e.closest('[data-admin-form-embed-container]').data('admin-form-embed-container');
          $(`input[name="${name}"]`).val('');
        }
      });
    });
    wait.on('[data-admin-form-embed-add]', (addEmbedBtn) => {
      $(addEmbedBtn).click(() => {
        modal.prompt(locale('Enter embed URL'), 'URL').then((url) => {
          const container = $(addEmbedBtn).closest('[data-admin-form-embed-container]')
          const name = container.data('admin-form-embed-container');

          let embedInput = $(`input[name="${name}"]`);
          if (!embedInput.length) {
            embedInput = $(`input[name="${name}[value]"]`);
          }

          embedInput.val(url);
          container.find('[data-admin-form-embed-value]').html(
            `<div data-admin-form-embed data-admin-embed-id="${name}" data-admin-embed-src="${url}"></div>`
          );
        });
      });
    });
  });
});