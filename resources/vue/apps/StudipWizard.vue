<template>
    <div>
        <nav class="wizard-navigation">
            <ul class="wizard-progress">
                <li v-for="(step, index) in visibleSteps" :key="index" :class="{ active: index === currentStep }">
                    <button :title="step.title"
                            role="tab"
                            :aria-selected="index === currentStep"
                            aria-controls="basic"
                            tabindex="0"
                            @click.prevent="jumpToStep(index)">
                        <template v-if="step.icon !== ''">
                            <studip-icon v-if="index === currentStep"
                                         :shape="step.icon"
                                         role="info_alt"
                                         :size="24"></studip-icon>
                            <studip-icon v-else
                                         :shape="step.icon"
                                         role="clickable"
                                         :size="24"></studip-icon>
                        </template>
                    </button>
                </li>
            </ul>
        </nav>
        <h2>
            {{ visibleSteps[currentStep].title }}
        </h2>
        <div ref="node" data-vue-app></div>
        <footer class="wizard-buttons">
            <button v-if="currentStep !== 0"
                    class="button back-button"
                    @click.prevent="jumpToStep(currentStep - 1)"
            >
                &lt;&lt; {{ $gettext('Zurück') }}
            </button>
            <button v-if="currentStep < steps.length - 1"
                    class="button forward-button"
                    @click.prevent="jumpToStep(currentStep + 1)"
            >
                {{ $gettext('Weiter') }} &gt;&gt;
            </button>
            <button v-if="currentStep === steps.length - 1"
                    class="button forward-button"
                    @click.prevent="finishWizard"
            >
                {{ $gettext('Abschließen') }} &gt;&gt;
            </button>
        </footer>
    </div>
    <Teleport to="#wizard-sidebar">
        <sidebar-widget id="wizard-widget"
                        :title="$gettext('Assistent')">
            <template #content>
                <ul class="widget-list widget-links sidebar-views">
                    <li v-for="(step, index) in visibleSteps"
                        :key="index"
                        :class="{active: index === currentStep}"
                    >
                        <button class="undecorated" @click.prevent="jumpToStep(index)"
                           :title="step.title ? step.title : ($gettext('Schritt ') + (index + 1))"
                        >
                            {{ step.title ? step.title : ($gettext('Schritt ') + (index + 1)) }}
                        </button>
                    </li>
                </ul>
            </template>
        </sidebar-widget>
    </Teleport>
</template>

<script setup>
import {onMounted, ref} from 'vue';
import {$gettext} from '@/assets/javascripts/lib/gettext';
import {useWizardStore} from '@/vue/store/pinia/wizardStore';
import SidebarWidget from '@/vue/components/SidebarWidget';

const props = defineProps({
    steps: {
        type: Array,
        required: true
    },
    showAllSteps: {
        type: Boolean,
        value: true
    }
});

// Reference to the DOM node where the included components will be mounted
const node = ref(null);
// Number of the current step
const currentStep = ref(0);
// HTML content of current step
let stepContent = ref('');
let mountedInstance = null;

const visibleSteps = ref(props.showAllSteps ? props.steps : [props.steps[0]]);

const store = useWizardStore();

const jumpToStep = (number) => {
    if (!visibleSteps.value.includes(props.steps[number])) {
        visibleSteps.value[number] = props.steps[number];
    }
    if (mountedInstance !== null) {
        //mountedInstance.unmount();
        //mountedInstance.submit(new Event('submit'));
    }
    currentStep.value = number;
    initializeContent(number);
};

const finishWizard = () => {
    alert('The wizard will rest now.');
};

const initializeContent = async (stepNumber) => {
    if (props.steps[stepNumber].type === 'Studip\\Forms\\Form') {
        STUDIP.Forms.create(node.value.childNodes);
    } else if (props.steps[stepNumber].type === 'Studip\\VueApp') {
        STUDIP.Vue.mountApp(node.value, props.steps[stepNumber].content);
    }
    stepContent.value = props.steps[stepNumber].content;
};

onMounted(() => {
    initializeContent(0);
    store.initialize();

    STUDIP.Vue.on('form.mounted', (instance) => {
        mountedInstance = instance;
    });
    STUDIP.Vue.on('vueApp.mounted', (instance) => {
        const internal = instance.$;
        const component = internal.type;
        console.log('Mounted instance', component);
        mountedInstance = instance;
    });
    STUDIP.Vue.on('form.emitValues', (values) => {
        store.setValues(currentStep.value, values);
    });
});

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
footer.wizard-buttons {
    background-color: var(--color--content-box-header);
    display: flex;
    padding-left: 15px;
    padding-right: 15px;

    .back-button {
        margin-right: auto;
    }

    .forward-button {
        margin-left: auto;
    }
}
</style>
