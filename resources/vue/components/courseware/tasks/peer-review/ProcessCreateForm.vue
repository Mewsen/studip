<template>
    <form class="default" @submit.prevent="">
        <div class="peer-review-process-create-form-switcher">
            <button
                class="button"
                :class="{ active: !showCustomConfiguration }"
                @click="showCustomConfiguration = false"
            >
                {{ $gettext('Einfach') }}
            </button>
            <button class="button" :class="{ active: showCustomConfiguration }" @click="showCustomConfiguration = true">
                {{ $gettext('Erweitert') }}
            </button>
        </div>

        <section class="peer-review-process-create-form-type-cards" v-if="!showCustomConfiguration">
            <article
                v-for="(configurationSet, index) in configurationSets"
                :key="index"
                :class="{ selected: selectedConfigurationSet === index }"
            >
                <h2>{{ configurationSet.name }}</h2>

                <button
                    class="button"
                    :class="{ accept: selectedConfigurationSet === index }"
                    :disabled="selectedConfigurationSet === index"
                    type="button"
                    @click="selectConfigurationSet(index)"
                >
                    {{ selectedConfigurationSet === index ? $gettext('Ausgewählt') : $gettext('Auswählen') }}
                </button>

                <PeerReviewProcessConfiguration :options="configurationSet.configuration" />
            </article>
        </section>

        <ContentBox
            v-else
            class="peer-review-process-create-form-custom-configuration"
            :title="$gettext('Erweiterte Einstellungen')"
        >
            <div class="custom-configuration">
                <div class="formpart">
                    <LabelRequired
                        :id="`peer-review-process-create-form-${uid}-anonymous`"
                        :label="$gettext('Anonymes oder offenes Review:')"
                    >
                        <select
                            v-model="localConfiguration.anonymous"
                            :id="`peer-review-process-create-form-${uid}-anonymous`"
                            @change="customizeConfiguration"
                        >
                            <option :value="true">{{ $gettext('anonym') }}</option>
                            <option :value="false">{{ $gettext('offen') }}</option>
                        </select>
                    </LabelRequired>
                </div>

                <div class="formpart">
                    <LabelRequired
                        :id="`peer-review-process-create-form-${uid}-duration`"
                        :label="$gettext('Bearbeitungszeitraum in Tagen:')"
                    >
                        <select
                            v-model.number="localConfiguration.duration"
                            :id="`peer-review-process-create-form-${uid}-duration`"
                            @change="customizeConfiguration"
                        >
                            <option v-for="i in 21" :key="i">{{ i }}</option>
                        </select>
                    </LabelRequired>
                </div>

                <div class="formpart">
                    <LabelRequired
                        :id="`peer-review-process-create-form-${uid}-type`"
                        :label="$gettext('Art des Reviews:')"
                    >
                        <select
                            v-model="localConfiguration.type"
                            :id="`peer-review-process-create-form-${uid}-type`"
                            @change="onChangeType"
                        >
                            <option v-for="[key, { short }] in Object.entries(reviewTypes)" :key="key" :value="key">
                                {{ short }}
                            </option>
                        </select>
                    </LabelRequired>
                </div>

                <div class="formpart">
                    <LabelRequired
                        :id="`peer-true-process-create-form-${uid}-anonymous`"
                        :label="$gettext('Review-Paarungen')"
                    >
                        <select
                            v-model="localConfiguration.automaticPairing"
                            :id="`peer-review-process-create-form-${uid}-automatic-pairing`"
                            @change="customizeConfiguration"
                        >
                            <option :value="true">{{ $gettext('Zufall') }}</option>
                            <option :value="false">{{ $gettext('Manuell') }}</option>
                        </select>
                    </LabelRequired>
                </div>
            </div>
        </ContentBox>
    </form>
</template>

<script>
import ContentBox from '../../../StudipContentBox.vue';
import LabelRequired from '../../../forms/LabelRequired.vue';
import PeerReviewProcessConfiguration from './ProcessConfiguration.vue';
import { ASSESSMENT_TYPES, CONFIGURATION_SETS, ProcessConfiguration } from './process-configuration';

let nextId = 0;

export default {
    components: { ContentBox, LabelRequired, PeerReviewProcessConfiguration },
    props: {
        configuration: {
            required: true,
            type: Object,
        },
        custom: {
            type: Boolean,
            default: false,
        },
    },
    data() {
        return {
            localConfiguration: { ...this.configuration },
            selectedConfigurationSet: -1,
            showCustomConfiguration: this.custom,
            uid: nextId++,
        };
    },
    computed: {
        reviewTypes: () => ASSESSMENT_TYPES,
        configurationSets: () => CONFIGURATION_SETS,
    },
    methods: {
        customizeConfiguration() {
            this.selectedConfigurationSet = -1;
            this.update();
        },
        findSelectedConfigurationSet() {
            this.selectedConfigurationSet = this.configurationSets.findIndex(({ configuration }) =>
                _.isEqual(this.configuration, configuration)
            );
        },
        onChangeType() {
            this.localConfiguration.payload =
                this.localConfiguration.type === this.configuration.type
                    ? this.configuration.payload
                    : ASSESSMENT_TYPES[this.localConfiguration.type].defaultPayload;
            this.customizeConfiguration();
        },
        resetData() {
            this.localConfiguration = { ...this.configuration };
            this.findSelectedConfigurationSet();
        },
        selectConfigurationSet(configurationSetIndex) {
            this.selectedConfigurationSet = configurationSetIndex;
            this.localConfiguration = CONFIGURATION_SETS[configurationSetIndex].configuration;
            this.update();
        },
        update() {
            this.$emit('update', this.localConfiguration);
        },
    },
    mounted() {
        this.findSelectedConfigurationSet();
    },
    watch: {
        configuration() {
            this.resetData();
        },
    },
};
</script>

<style scoped lang="scss">
.peer-review-process-create-form-type-cards {
    box-sizing: border-box;
    width: 100%;
    margin-block: 1.5rem 0;

    display: flex;
    flex-wrap: wrap;
    gap: 1.5rem;
    --threshold: 45rem;

    article {
        flex-grow: 1;
        flex-basis: calc((var(--threshold) - 100%) * 999);
        box-sizing: border-box;
        padding: 1rem;
        border: 2px var(--dark-gray-color-20) solid;

        &.selected {
            border-color: var(--dark-gray-color-80);
            border-width: 2px;
        }

        h2 {
            font-weight: bold;
            font-size: 1.2rem;
            margin-block: 1rem 0;
        }
        button {
            margin-block: 1.5rem;
        }
        ul {
            padding-inline: 1em 0;
        }
        li {
            padding-block: 0.5rem;
        }
    }

    > :nth-last-child(n + 4),
    > :nth-last-child(n + 4) ~ * {
        flex-basis: 100%;
    }
}

.peer-review-process-create-form-type-cards + section {
    text-align: center;
    margin-block-end: 1.5rem;
}

.peer-review-process-create-form-custom-configuration {
    margin-block: 1.5rem;
}

.custom-configuration {
    padding: 1rem;
}

.peer-review-process-create-form-switcher {
    display: flex;
    justify-content: center;
}

.peer-review-process-create-form-switcher button {
    margin: 0;
}

.peer-review-process-create-form-switcher button + button {
    border-left: none;
}

.peer-review-process-create-form-switcher button.active {
    background: var(--base-color);
    color: var(--white);
    cursor: default;
}

.peer-review-process-create-form-switcher button:not(.active):hover {
    background: var(--white);
    color: var(--base-color);
}
</style>
