STUDIP.ready(async () => {
    const vueAppNodes = document.querySelectorAll('[data-vue-app]:not([data-vue-app-created])');
    for (const node of vueAppNodes) {
        await STUDIP.Vue.mountApp(node);
    }
});
