STUDIP.ready(() => {
    document.querySelectorAll('[data-vue-app]:not([data-vue-app-created])').forEach((node) => {
        const config = Object.assign(
            {
                components: [],
                stores: {}
            },
            JSON.parse(node.dataset.vueApp)
        );

        let components = {};
        config.components.forEach(component => {
            const name = component.split('/').reverse()[0];
            components[name] = () => {
                // TODO: I wonder if this works with Vue3

                const temp = import(`../../../vue/components/${component}.vue`);
                temp.then(({default: c}) => {
                    const mounted = c.mounted ?? null;
                    c.mounted = function (...args) {
                        if (
                            this.$el instanceof Element
                            && this.$el.querySelector('[data-dialog-button]')
                        ) {
                            this.$el.closest('.studip-dialog')
                                .querySelector('.ui-dialog-buttonpane')
                                .remove();
                        }
                        if (mounted) {
                            mounted.call(this, args);
                        }
                    };
                    return c;
                })
                return temp;
            };
        });

        STUDIP.Vue.load().then(async ({createApp, store}) => {
            for (const [index, name] of Object.entries(config.stores)) {
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
                });
            }
            createApp({
                components,
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
                beforeDestroy() {
                    STUDIP.Vue.emit('VueAppWillDestroy', this);
                },
                destroyed() {
                    STUDIP.Vue.emit('VueAppDidDestroy', this);
                },
            }).$mount(node);
        });

        node.dataset.vueAppCreated = 'true';
    });
});
