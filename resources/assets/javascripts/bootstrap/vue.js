STUDIP.ready(async () => {
    const vueAppNodes = document.querySelectorAll('[data-vue-app]:not([data-vue-app-created])');
    for (const node of vueAppNodes) {
        await mountVueApp(node);
    }
});

async function mountVueApp(node) {
    node.dataset.vueAppCreated = 'true';

    const config = parseVueAppConfig(node);
    if (!config) {
        return;
    }

    const { createApp, h, store } = await STUDIP.Vue.load();

    const [appComponent, plugins] = await loadAppDependencies(config, store);
    const app = createApp({
        render: () =>
            h(
                addDialogButtonRemoval(appComponent),
                config.props,
                Object.fromEntries(
                    Object.entries(config.slots).map(([slot, template]) => {
                        return [slot, () => h({ template })];
                    }),
                ),
            ),
        ...createLifecycleHooks(),
    });

    plugins.forEach((plugin) => app.use(plugin, { store }));

    app.mount(node);

    handleDialogClose(node, app);
}

function parseVueAppConfig(node) {
    const dataElement = node.querySelector('script[type="application/json"]');
    if (!dataElement) {
        console.error('Missing data for vue app');
        return null;
    }

    return Object.assign(
        {
            appPath: null,
            plugins: [],
            props: {},
            slots: {},
            stores: {},
            storeData: {},
            vuexStores: {},
            vuexStoreData: {},
        },
        JSON.parse(dataElement.innerText),
    );
}

async function loadAppDependencies(config, store) {
    const promises = [
        import(`@/vue/apps/${config.appPath}.vue`),
        initializePlugins(config),
        ...initializeVuexStores(config, store),
        ...initializePiniaStores(config),
    ];

    const [{ default: appComponent }, plugins = []] = await Promise.all(promises);
    return [appComponent, plugins];
}

function createLifecycleHooks() {
    return {
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
    };
}

function handleDialogClose(node, app) {
    const dialog = node.closest('.studip-dialog');
    if (dialog !== null) {
        $(dialog).on('dialogclose', () => app.unmount());
    }
}

function initializeVuexStores(config, store) {
    return Object.entries(config.vuexStores).map(([index, name]) =>
        import(`@/vue/store/${name}`).then(({ default: storeConfig }) => {
            if (!store.hasModule(index)) {
                store.registerModule(index, storeConfig);
            }
            Object.entries(config.vuexStoreData[index]).forEach(([type, payload]) =>
                store.commit(`${index}/${type}`, payload),
            );
        }),
    );
}

function initializePiniaStores(config) {
    return Object.entries(config.stores).map(([name, command]) =>
        import(`@/vue/store/pinia/${name}`).then((storeConfig) => {
            const piniaStore = storeConfig[command]();
            applyPiniaStoreData(piniaStore, config.storeData);
        }),
    );
}

function applyPiniaStoreData(piniaStore, data) {
    Object.entries(data).forEach(([command, value]) => {
        if (_.isFunction(piniaStore[command])) {
            piniaStore[command](value);
        } else {
            piniaStore[command] = value;
        }
    });
}

function initializePlugins(config) {
    return Promise.all(
        Object.entries(config.plugins).map(([plugin, filename]) =>
            import(`@/vue/plugins/${filename}.js`).then((temp) => temp[plugin]),
        ),
    );
}

function addDialogButtonRemoval(component) {
    const originalMounted = component.mounted;
    component.mounted = function (...args) {
        if (
            this.$el instanceof Element &&
            this.$el.closest('.studip-dialog') &&
            this.$el.querySelector('[data-dialog-button]')
        ) {
            this.$el.closest('.studip-dialog').querySelector('.ui-dialog-buttonpane')?.remove();
        }
        if (originalMounted) {
            originalMounted.call(this, args);
        }
    };
    return component;
}
