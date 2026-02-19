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
                        <template v-else>
                            <div class="step-number">
                                {{ index + 1 }}
                            </div>
                        </template>
                    </button>
                </li>
            </ul>
        </nav>
        <h2>
            {{ visibleSteps[currentStep].title }}
        </h2>
        <div v-if="currentStepType === 'app'" ref="node" data-vue-app></div>
        <div v-if="currentStepType === 'form'" ref="node" v-html="stepContent"></div>
        <component v-if="currentStepType === 'vue'" ref="node" :is="stepContent"></component>
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
    <teleport to="#wizard-widget">
        <sidebar-widget :title="$gettext('Assistent')" class="sidebar-navigation">
            <template #content>
                <ul class="widget-list widget-links sidebar-navigation">
                    <li
                        v-for="(step, index) in visibleSteps"
                        :key="index"
                        :class="{ active: index === currentStep }"
                    >
                        <a @click="jumpToStep(index)">
                            <template v-if="step.icon !== ''">
                                <studip-icon :shape="step.icon"
                                             role="clickable"
                                             :size="24"></studip-icon>
                            </template>
                            <template v-else>
                                {{ index + 1 }}.
                            </template>
                            {{ step.title }}
                        </a>
                        <div
                            v-if="index === currentStep && step.description !== ''"
                            class="wizard-part-description"
                        >
                            {{ step.description }}
                        </div>
                    </li>
                </ul>
            </template>
        </sidebar-widget>
    </teleport>
</template>

<script setup>
import {nextTick, onMounted, ref} from 'vue';
import {$gettext} from '@/assets/javascripts/lib/gettext';

import StockImages from './StockImages';
import SidebarWidget from "../components/SidebarWidget.vue";

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
let mountedApp = null;
const currentStepType = ref(null);

const visibleSteps = ref(props.showAllSteps ? props.steps : [props.steps[0]]);

const checkValidity = () => {
    if (currentStepType.value === 'form') {
        STUDIP.Vue.emit('form.submit', props.steps[currentStep.value].id);
    }
};

const jumpToStep = (number) => {
    checkValidity();

    if (mountedApp !== null) {
        mountedApp.unmount();
    }

    if (!visibleSteps.value.includes(props.steps[number])) {
        visibleSteps.value[number] = props.steps[number];
    }
    currentStep.value = number;
    initializeContent(number);
};

const finishWizard = () => {
    checkValidity();
};

const initializeContent = async (stepNumber) => {
    if (props.steps[stepNumber].type === 'Studip\\Forms\\Form') {
        currentStepType.value = 'form';
        stepContent.value = props.steps[stepNumber].content;
        nextTick(() => {
            STUDIP.Forms.create(node.value.childNodes);
        });
    } else if (props.steps[stepNumber].type === 'Studip\\VueApp') {
        currentStepType.value = 'app';
        stepContent.value = JSON.parse(props.steps[stepNumber].content);
        nextTick(() => {
            STUDIP.Vue.mountApp(node.value, props.steps[stepNumber].content);
        });
    } else if (props.steps[stepNumber].type === 'Vue') {
        currentStepType.value = 'vue';
        stepContent.value = props.steps[stepNumber].content;
    }
};

onMounted(() => {
    initializeContent(0);

    STUDIP.Vue.on('form.mounted', (mounted) => {
        mountedApp = mounted.app;
    });
    STUDIP.Vue.on('vueApp.mounted', (mounted) => {
        if (mounted.config.appPath === stepContent.value.appPath) {
            mountedApp = mounted.app;
        }
    });

    visibleSteps.value.push({
        type: 'Vue',
        id: 'stock-images',
        title: 'Bilderpool',
        content: StockImages,
        icon: 'block-gallery'
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

            .step-number {
                font-weight: 700;
                color: var(--color--font-inverted);
            }
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
    padding-left: 15px;
    padding-right: 15px;

    .forward-button {
        margin-left: 15px;

        &:first-of-type {
            margin-left: 0;
        }
    }
}
ul.widget-list > li > .wizard-part-description {
    padding: 4px 0 4px 8px;
}
</style>
