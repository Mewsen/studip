import { defineAsyncComponent } from 'vue';

function attachComponents(app, configuredComponents) {
    configuredComponents.forEach(component => {
        const name = component.split('/').reverse()[0];
        app.component(name, defineAsyncComponent(() => {
            const temp = import(`../../../vue/components/${component}.vue`);
            temp.then(({default: c}) => {
                const mounted = c.mounted ?? null;
                c.mounted = function (...args) {
                    if (
                        this.$el instanceof Element
                        && this.$el.closest('.studip-dialog')
                        && this.$el.querySelector('[data-dialog-button]')
                    ) {
                        this.$el.closest('.studip-dialog')
                            .querySelector('.ui-dialog-buttonpane')
                            ?.remove();
                    }
                    if (mounted) {
                        mounted.call(this, args);
                    }
                };
                return c;
            })
            return temp;
        }));
    });
}

STUDIP.ready(() => {
    document.querySelectorAll('[data-vue-app]:not([data-vue-app-created])').forEach(async (node) => {
        node.dataset.vueAppCreated = 'true';

        const config = Object.assign(
            {
                components: [],
                plugins: {},
                stores: {}
            },
            JSON.parse(node.dataset.vueApp)
        );

        const { createApp, store } = await STUDIP.Vue.load();

        const promises = [Promise.resolve()];

        for (const [index, name] of Object.entries(config.stores)) {
            promises.push(
                import(`../../../vue/store/${name}.js`).then(storeConfig => {
                    store.registerModule(index, storeConfig.default);

                    const dataElement = document.getElementById(`vue-store-data-${index}`);
                    if (dataElement) {
                        const data = JSON.parse(dataElement.innerText);
                        Object.keys(data).forEach(command => {
                            store.commit(`${index}/${command}`, data[command]);
                        });

                        dataElement.remove();
                    }
                })
            );
        }

        const plugins = [];
        for (const [plugin, filename] of Object.entries(config.plugins)) {
            promises.push(
                import(`../../../vue/plugins/${filename}.js`)
                .then((temp) => plugins.push(temp[plugin]))
            );
        }

        await Promise.all(promises);

        const app = createApp({
            store,

            beforeCreate() {
                STUDIP.Vue.emit('VueAppWillCreate', this);
            },
            created() {
                STUDIP.Vue.emit('VueAppDidCreate', this);
            },
            beforeMount() {
                STUDIP.Vue.emit('VueAppWillMount', this);
            },
            mounted() {
                STUDIP.Vue.emit('VueAppDidMount', this);
            },
            beforeUpdate() {
                STUDIP.Vue.emit('VueAppWillUpdate', this);
            },
            updated() {
                STUDIP.Vue.emit('VueAppDidUpdate', this);
            },
            beforeUnmount() {
                STUDIP.Vue.emit('VueAppWillUnmount', this);
            },
            unmounted() {
                STUDIP.Vue.emit('VueAppDidUnmount', this);
            },
        });

        attachComponents(app, config.components);
        plugins.forEach(plugin => app.use(plugin, { store }))

        app.mount(node);
    });
});
