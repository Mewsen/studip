STUDIP.domReady(function () {
    STUDIP.Dialog.initialize();
});

$(document).on('click', '[data-vue-app] [data-dialog-button] .cancel.button', () => {
    STUDIP.Dialog.close();
    return false;
});
