$(document).ready(() => {
  function getYouTubeVideoId(url) {
    try {
      const parsedUrl = new URL(url);
      const hostname = parsedUrl.hostname.replace('www.', '');
      if (hostname === 'youtube.com') {
        return parsedUrl.searchParams.get('v');
      }
      if (hostname === 'youtu.be') {
        return parsedUrl.pathname.slice(1);
      }
      if (hostname === 'youtube-nocookie.com') {
        const parts = parsedUrl.pathname.split('/');
        return parts.includes('embed') ? parts.pop() : null;
      }
      return null;
    } catch (e) {
      return null;
    }
  }

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

          const videoId = getYouTubeVideoId(url);
          if (videoId) {
            url = "https://www.youtube.com/embed/" + videoId;
          }

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