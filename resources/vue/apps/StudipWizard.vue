<template>
    <div>
        <nav class="wizard-navigation">
            <ul class="wizard-progress">
                <li v-for="(step, index) in wizardSteps" :key="index" :class="{ active: index === currentStep }">
                    <button :title="currentStepName"
                            role="tab"
                            :aria-selected="index === currentStep"
                            aria-controls="basic"
                            tabindex="0"
                            @click.prevent="jumpToStep(index)">
                        <studip-icon v-if="step.icon"
                                     :shape="step.icon"
                                     :role="index === currentStep ? 'info_alt' : 'clickable'"
                                     :size="24"
                                     :key="NaN"></studip-icon>
                    </button>
                </li>
            </ul>
        </nav>
        <h2>
            {{ currentStepName }}
        </h2>
        <div ref="stepData" class="wizard-content"></div>
        <div class="buttons">
            <button v-if="currentStep !== 0" class="button" @click.prevent="jumpToStep(currentStep - 1)">
                &lt;&lt; {{ $gettext('Zurück') }}
            </button>
            <button v-if="currentStep < wizardSteps.length - 1" class="button" @click.prevent="jumpToStep(currentStep + 1)">
                {{ $gettext('Weiter') }} &gt;&gt;
            </button>
            <button v-if="currentStep === wizardSteps.length - 1" class="button" @click.prevent="finishWizard">
                {{ $gettext('Abschließen') }} &gt;&gt;
            </button>
        </div>
    </div>
</template>

<script setup>
import {onMounted, ref, computed} from 'vue';
import {$gettext} from "../../assets/javascripts/lib/gettext";

const props = defineProps({
    steps: {
        type: Array,
        required: true
    }
});

// Reactive version of step props
let wizardSteps = ref(props.steps);
// Reference to the DOM node where the included components will be mounted
let stepData = ref(null);
// Number of the current step
let currentStep = ref(0);
// Currently mounted app
let currentApp = null;

const jumpToStep = (number) => {
    currentStep.value = number;
    currentApp.unmount();
    mountVueApp(wizardSteps.value[currentStep.value].content, stepData.value);
};

const finishWizard = () => {
    alert('The wizard will rest now.');
}

const currentStepName = computed(() => {
    let name = $gettext('Schritt %{step}', {step: currentStep.value + 1});

    if (wizardSteps.value[currentStep.value].name) {
        name += ': '  + wizardSteps.value[currentStep.value].name;
    }

    return name;
});

onMounted(() => {
    mountVueApp(wizardSteps.value[currentStep.value].content, stepData.value)
        .then(() => {
            currentApp = stepData.value.__vue_app__;
        });
});

async function mountVueApp(appConfig, node) {
    const config = parseVueAppConfig(appConfig);
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
    node.__vue_app__ = app;

    handleDialogClose(node, app);
}

function parseVueAppConfig(config) {
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
        JSON.parse(config)
    );
}

async function loadAppDependencies(config, store) {
    const promises = [
        import(`@/vue/apps/${config.appPath}.vue`),
        ...initializePlugins(config),
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
    return Object.entries(config.plugins).map(([plugin, filename]) =>
        import(`@/vue/plugins/${filename}.js`).then((temp) => temp[plugin]),
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
</script>

<style scoped lang="scss">
.wizard-progress {
    display: flex;
    list-style: none;
    gap: 62px;
    margin: 1.5em 0 2.5em 0;
    padding: 0;

    li {
        border: 2px dotted var(--color--highlight);
        display: inline-block;
        position: relative;

        button {
            background-color: unset;
            border: 0;
            height: 44px;
            padding: 6px;
            width: 44px;
        }

        &.active button {
            background-color: var(--color--highlight);
        }

        &:not(:last-of-type)::before {
            position: absolute;
            content: "";
            width: 62px;
            border: solid thin var(--color--highlight);
            top: 50%;
            -webkit-transform: translateY(-50%);
            transform: translateY(-50%);
            left: 100%;
        }

        &.active {
            border-style: solid;
        }
    }
}
.wizard-content {
    margin-bottom: 15px;
    margin-top: 15px;
}
</style>
