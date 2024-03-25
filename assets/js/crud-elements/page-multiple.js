$(document).ready(() => {
  $(document).on('click', '[data-admin-form-element-multiple-page-add-item]', (e) => {
    const id = Date.now() * Math.random();
    const element = $(e.currentTarget).closest('[data-admin-form-element-multiple-page]');

    const template = element.find('[data-admin-form-element-multiple-page-template]').html()
      .replaceAll('{{pageId}}', id);

    $(e.currentTarget).after(template);
  });

  $(document).on('click', '[data-admin-form-element-multiple-page-item-remove]', (e) => {
    const container = $(e.currentTarget).closest('[data-admin-form-element-group-multiple-page-item-container]');

    modal.question('Remove block?').then(() => {
      const next = container.next();
      container.remove();
      next.remove();
    });
  });
});