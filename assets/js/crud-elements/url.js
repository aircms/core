const slug = (str, options = {}) => {
  const defaults = {
    delimiter: '-',
    limit: null,
    lowercase: true,
    replacements: {},
    transliterate: true,
  };

  options = {...defaults, ...options};

  const charMap = {
    'À': 'A', 'Á': 'A', 'Â': 'A', 'Ã': 'A', 'Ä': 'A', 'Å': 'A', 'Æ': 'AE', 'Ç': 'C',
    'È': 'E', 'É': 'E', 'Ê': 'E', 'Ë': 'E', 'Ì': 'I', 'Í': 'I', 'Î': 'I', 'Ï': 'I',
    'Ð': 'D', 'Ñ': 'N', 'Ò': 'O', 'Ó': 'O', 'Ô': 'O', 'Õ': 'O', 'Ö': 'O', 'Ő': 'O',
    'Ø': 'O', 'Ù': 'U', 'Ú': 'U', 'Û': 'U', 'Ü': 'U', 'Ű': 'U', 'Ý': 'Y', 'Þ': 'TH',
    'ß': 'ss',
    'à': 'a', 'á': 'a', 'â': 'a', 'ã': 'a', 'ä': 'a', 'å': 'a', 'æ': 'ae', 'ç': 'c',
    'è': 'e', 'é': 'e', 'ê': 'e', 'ë': 'e', 'ì': 'i', 'í': 'i', 'î': 'i', 'ï': 'i',
    'ð': 'd', 'ñ': 'n', 'ò': 'o', 'ó': 'o', 'ô': 'o', 'õ': 'o', 'ö': 'o', 'ő': 'o',
    'ø': 'o', 'ù': 'u', 'ú': 'u', 'û': 'u', 'ü': 'u', 'ű': 'u', 'ý': 'y', 'þ': 'th',
    'ÿ': 'y',
    '©': '(c)',

    'А': 'A', 'Б': 'B', 'В': 'V', 'Г': 'G', 'Д': 'D', 'Е': 'E', 'Ё': 'Yo', 'Ж': 'Zh',
    'З': 'Z', 'И': 'I', 'Й': 'J', 'К': 'K', 'Л': 'L', 'М': 'M', 'Н': 'N', 'О': 'O',
    'П': 'P', 'Р': 'R', 'С': 'S', 'Т': 'T', 'У': 'U', 'Ф': 'F', 'Х': 'H', 'Ц': 'C',
    'Ч': 'Ch', 'Ш': 'Sh', 'Щ': 'Sh', 'Ы': 'Y', 'Э': 'E', 'Ю': 'Yu', 'Я': 'Ya',
    'а': 'a', 'б': 'b', 'в': 'v', 'г': 'g', 'д': 'd', 'е': 'e', 'ё': 'yo', 'ж': 'zh',
    'з': 'z', 'и': 'i', 'й': 'j', 'к': 'k', 'л': 'l', 'м': 'm', 'н': 'n', 'о': 'o',
    'п': 'p', 'р': 'r', 'с': 's', 'т': 't', 'у': 'u', 'ф': 'f', 'х': 'h', 'ц': 'c',
    'ч': 'ch', 'ш': 'sh', 'щ': 'sh', 'ы': 'y', 'э': 'e', 'ю': 'yu', 'я': 'ya',
  };

  for (const [pattern, replacement] of Object.entries(options.replacements)) {
    const regex = new RegExp(pattern, 'g');
    str = str.replace(regex, replacement);
  }

  if (options.transliterate) {
    str = str.split('').map(char => charMap[char] || char).join('');
  }

  str = str.replace(/[^a-zA-Z0-9]+/g, options.delimiter);

  const delimiterEscaped = options.delimiter.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&');
  const multipleDelimiters = new RegExp(`${delimiterEscaped}{2,}`, 'g');
  str = str.replace(multipleDelimiters, options.delimiter);

  if (options.limit) {
    str = str.substring(0, options.limit);
  }

  str = str.replace(new RegExp(`^${delimiterEscaped}|${delimiterEscaped}$`, 'g'), '');

  return options.lowercase ? str.toLowerCase() : str;
}

const getTitle = ($el, selector = 'title') => {
  let title = null;
  $el.closest('[data-admin-form-element-group-multiple-group-item-container]').find('[name*="' + selector + '"]').each(function () {
    if (!title) {
      title = $(this).val();
    }
  });
  if (!title) {
    $el.closest('[data-admin-tab-content-item]').find('[name*="' + selector + '"]').each(function () {
      if (!title) {
        title = $(this).val();
      }
    });
  }
  if (!title) {
    $el.closest('[data-admin-tab-content]').find('[name*="' + selector + '"]').each(function () {
      if (!title) {
        title = $(this).val();
      }
    });
  }
  return title;
}

$(document).ready(() => {
  $(document).on('click', '[data-admin-form-url-generate]', function () {
    let title = getTitle($(this));
    $(this).closest('[data-admin-form-url-container]').find('input').val(slug(title)).focus();
  });
});