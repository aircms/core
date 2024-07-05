class Tiny {
  static isReady = false;
  static fonts = [];

  static ready(cb) {
    if (!Tiny.isReady) {
      $.get('/_fonts/fonts', (fonts) => {
        Tiny.fonts = fonts;
        cb && cb();
      });
    } else {
      cb && cb();
    }
  }

  options = {
    menubar: 'edit insert view format table tools html',
    toolbar_sticky: false,
    toolbar_items_size: 'small',
    plugins: `advlist anchor link lists fullscreen code paste textcolor`,
    toolbar: 'styles fontfamily fontsize forecolor backcolor bold italic underline strikethrough align link bullist numlist code fullscreen',
    style_formats: [
      {title: 'Heading 1', block: 'h1'},
      {title: 'Heading 2', block: 'h2'},
      {title: 'Heading 3', block: 'h3'},
      {title: 'Heading 4', block: 'h4'},
      {title: 'Heading 5', block: 'h5'},
      {title: 'Paragraph', block: 'p'},
    ],
    paste_as_text: true,
    object_resizing: true,
    image_caption: true,
    language: $('html').attr('lang') === 'ua' ? 'uk' : 'en',

    font_size_formats: "8pt 9pt 10pt 11pt 12pt 14pt 18pt 24pt 30pt 36pt 48pt 60pt 72pt 96pt",
    font_family_formats: "Arial=arial,helvetica,sans-serif; " + Tiny.fonts,
    content_style: "@import url('/_fonts/css');",

    setup: (editor) => editor.on('change', () => editor.save())
  }

  constructor(selector, options, cb) {
    if (selector instanceof HTMLElement) {
      const id = Math.random();
      $(selector).attr('data-admin-form-tiny-selector', id);
      selector = '[data-admin-form-tiny-selector="' + id + '"]';
    }

    this.options.selector = selector;

    if (theme.theme === 'dark') {
      this.options.skin = 'oxide-dark';
      this.options.content_css = 'dark';
    }

    if (options.autosize) {
      this.options.plugins += ' autoresize';
    }

    tinymce.init({...this.options, ...options}).then(() => {
      cb && cb();
    });
  }
}

Tiny.ready(() => {
  wait.on('[data-admin-form-tiny]', (tiny) => new Tiny(tiny, {
    autosize: true
  }));
});