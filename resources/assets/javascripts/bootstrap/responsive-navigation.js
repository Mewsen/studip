import ResponsiveNavigation from '../../../vue/components/responsive/ResponsiveNavigation.vue';

STUDIP.domReady(() => {
    STUDIP.Vue.load().then(({ createApp }) => {
        createApp({
            el: '#responsive-menu',
            components: { ResponsiveNavigation }
        });
    });
});
