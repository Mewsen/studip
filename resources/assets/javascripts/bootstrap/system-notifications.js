import SystemNotificationManager from '../../../vue/components/SystemNotificationManager.vue';

STUDIP.domReady(() => {
    document.getElementById('system-notifications')?.classList.add('vueified');
    STUDIP.Vue.load().then(({ createApp }) => {
        createApp({
            el: '#system-notifications',
            components: { SystemNotificationManager }
        });
    });
});
