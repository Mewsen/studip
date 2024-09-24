import StudipTree from '../../../vue/components/tree/StudipTree.vue'

STUDIP.ready(() => {
    document.querySelectorAll('[data-studip-tree]:not(.vueified)').forEach(element => {
        element.classList.add('vueified');
        STUDIP.Vue.load().then(({ createApp }) => {
            createApp({
                el: element,
                components: { StudipTree }
            })
        })
    });
});
