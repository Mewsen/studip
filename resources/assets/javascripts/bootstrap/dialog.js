STUDIP.domReady(function () {
    STUDIP.Dialog.initialize();
});

document.addEventListener(
    'click',
    (event) => {
        if (event.target.matches('.studip-dialog [data-vue-app] [data-dialog-button] .cancel.button')) {
            STUDIP.Dialog.close();
            event.preventDefault();
            event.stopPropagation();
        }
    },
    true
);
