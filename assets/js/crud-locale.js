$(document).ready(() => {
  wait.on('[data-locale]', (localelizedElement) => {
    $(localelizedElement).text(locale($(localelizedElement).data('locale')));
  });
});