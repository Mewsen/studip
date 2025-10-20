STUDIP.ready(function () {
    const selectors = [
        '.use-vue-components',
        'form .simplevue'
    ];
    const selector = selectors.map(selector => `${selector}:not(.vueified)`).join(',');

    const containers = document.querySelectorAll(selector);

    if (containers.length > 0) {
        STUDIP.Vue.load().then(({ createApp }) => {
            containers.forEach(container => {
                container.classList.add('vueified');
                createApp().mount(container)
            });
        });
    }
});
