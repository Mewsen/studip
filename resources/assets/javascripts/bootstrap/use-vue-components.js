STUDIP.ready(function () {
    const containers = document.querySelectorAll('.use-vue-components');

    STUDIP.Vue.load().then(({ createApp }) => {
        containers.forEach(container  => createApp().mount(container));
    });
});


