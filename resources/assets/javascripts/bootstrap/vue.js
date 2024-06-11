/**
 * The following block of code is used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */
STUDIP.ready(() => {
    $('[data-vue-app]').each(function () {
        if ($(this).is('[data-vue-app-created]')) {
            return;
        }

        const config = Object.assign({}, {
            id: false,
            components: [],
            store: false
        }, $(this).data().vueApp);

        let data = {};
        if (config.id && window.STUDIP.AppData && window.STUDIP.AppData[config.id] !== undefined) {
            data = window.STUDIP.AppData[config.id];
        }

        let components = {};
        config.components.forEach(component => {
            components[component] = () => import(`../../../vue/components/${component}.vue`);
        });

        STUDIP.Vue.load().then(async ({createApp, store}) => {
            if (config.store) {
                const storeConfig = await import(`../../../vue/store/${config.store}.js`);

                store.registerModule(config.id, storeConfig.default, {root: true});

                Object.keys(data).forEach(command => {
                    store.commit(`${config.id}/${command}`, data[command]);
                });
            }

            createApp({components, data}).$mount(this);
        });

        $(this).attr('data-vue-app-created', '');
    });
});
