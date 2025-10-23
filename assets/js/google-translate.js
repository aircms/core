$(document).ready(() => {
  if (window.googleTranslate.length) {

    const getPhrase = (phrase, language) => {
      return new Promise(resolve => {
        $.post('/' + window.googleTranslate + "/phrase", {
          phrase: phrase,
          language: language || getLanguage()
        }, (res) => {
          if (!res.translation) {
            notify.danger('Google Transla currenty is anavailable');
            return;
          }
          notify.success('Phrase replaced');
          resolve(res.translation);
        });
      });
    };

    wait.on('input.form-control, textarea.form-control', (el) => {
      if (
        $(el).attr('data-admin-datetimepicker') === undefined
        && $(el).attr('type') !== 'search'
        && !$(el).closest('.dropdown-menu').length
      ) {
        $(el).parent().append('<button data-localized class="localized"><i class="fas fa-globe"></i></button>');
      }
    });

    wait.on('[data-admin-form-tiny]', (el) => {
      $(el).append('<button data-localized-tiny class="localized"><i class="fas fa-globe"></i></button>');
    });

    $(document).on('click', '[data-localized-tiny]', function () {
      const container = $(this).closest('[data-admin-form-tiny]');
      getPhrase(container.find('textarea').val()).then((phrase) => {
        container.find('textarea').val(phrase);
        container.find('[data-admin-form-tiny-preview]').html(phrase);
      });
      return false;
    });

    $(document).on('click', '[data-localized]', function () {
      let input = $(this).parent().find('input');
      if (!input.length) {
        input = $(this).parent().find('textarea');
      }
      let language = getLanguage();
      if (input.length && input.data('phrase-language')) {
        language = input.data('phrase-language');
      }
      getPhrase(input.val(), language).then((phrase) => input.val(phrase));
      return false;
    });
  }
});
