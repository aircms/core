const copyToClipboard = (text) => {
  navigator.clipboard.writeText(text).catch(() => {
    modal.message('Oops... Looks like your browser doesn\'t support clipboard operations.', {style: 'danger'});
  });
};