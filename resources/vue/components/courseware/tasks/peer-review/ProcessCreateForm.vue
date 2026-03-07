<template>
    <form class="default" @submit.prevent="">
        <fieldset class="select_configuration_set">
            <template v-for="(configurationSet, index) in configurationSets" :key="`configuration-set-${index}`">
                <input
                    :aria-description="'todo'"
                    :checked="selectedConfigurationSet === index"
                    :id="`configuration_set_${index}`"
                    :value="index"
                    name="selected_configuration_set"
                    type="radio"
                />
                <label @click="selectConfigurationSet(index)">
                    <div class="icon">
                        <studip-icon
                            :shape="`radiobutton-${selectedConfigurationSet === index ? 'checked' : 'unchecked'}`"
                            :size="24"
                        />
                    </div>
                    <div class="text">
                        {{ configurationSet.name }}
                    </div>
                    <studip-icon shape="arr_1down" :size="24" class="arrow" />
                    <studip-icon shape="check-circle" :size="24" class="check" />
                </label>
                <div>
                    <PeerReviewProcessConfiguration :options="configurationSet.configuration" />
                </div>
            </template>

            <input
                :aria-description="'todo'"
                :checked="selectedConfigurationSet === null"
                id="configuration_set_custom"
                value="custom"
                name="selected_configuration_set"
                type="radio"
            />
            <label @click="selectConfigurationSet(null)">
                <div class="icon">
                    <studip-icon
                        :shape="`radiobutton-${selectedConfigurationSet === null ? 'checked' : 'unchecked'}`"
                        :size="24"
                    />
                </div>
                <div class="text">
                    {{ $gettext('Eigene Einstellungen') }}
                </div>
                <studip-icon shape="arr_1down" :size="24" class="arrow" />
                <studip-icon shape="check-circle" :size="24" class="check" />
            </label>
            <div class="peer-review-process-create-form-custom-configuration">
                <div class="custom-configuration">
                    <div class="formpart">
                        <LabelRequired
                            :id="`peer-review-process-create-form-${uid}-anonymous`"
                            :label="$gettext('Anonymes oder offenes Review:')"
                        >
                            <select
                                v-model="localConfiguration.anonymous"
                                :id="`peer-review-process-create-form-${uid}-anonymous`"
                                @change="update"
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
                                @change="update"
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
                                @change="update"
                            >
                                <option :value="true">{{ $gettext('Zufall') }}</option>
                                <option :value="false">{{ $gettext('Manuell') }}</option>
                            </select>
                        </LabelRequired>
                    </div>
                </div>
            </div>
        </fieldset>
    </form>
</template>

<script>
import LabelRequired from '../../../forms/LabelRequired.vue';
import PeerReviewProcessConfiguration from './ProcessConfiguration.vue';
import { ASSESSMENT_TYPES, CONFIGURATION_SETS } from './process-configuration';

let nextId = 0;

export default {
    components: { LabelRequired, PeerReviewProcessConfiguration },
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
            selectedConfigurationSet: 0,
            uid: nextId++,
        };
    },
    computed: {
        reviewTypes: () => ASSESSMENT_TYPES,
        configurationSets: () => CONFIGURATION_SETS,
    },
    methods: {
        customizeConfiguration() {
            this.update();
        },
        findSelectedConfigurationSet() {
            const index = this.configurationSets.findIndex(({ configuration }) =>
                _.isEqual(this.configuration, configuration),
            );
            this.selectedConfigurationSet = index === -1 ? null : index;
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
            // this.findSelectedConfigurationSet();
        },
        selectConfigurationSet(configurationSetIndex) {
            this.selectedConfigurationSet = configurationSetIndex;
            if (configurationSetIndex in CONFIGURATION_SETS) {
                this.localConfiguration = CONFIGURATION_SETS[configurationSetIndex].configuration;
            }
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
            this.localConfiguration = { ...this.configuration };
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

fieldset.select_configuration_set {
    border: none;
    padding: 0;
    margin: 0;

    > :not(legend) {
        margin: 0;
    }

    > input[type='radio'] {
        opacity: 0;
        position: absolute;
        &:focus + label {
            outline: auto;
        }
    }
    > label {
        cursor: pointer;
        border: 1px solid var(--content-color-40);
        transition: background-color var(--transition-duration);
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0 10px 0;
        margin: 0;
        border-top: none;
        > .text {
            width: 100%;
            margin-left: 10px;
        }
        > .check {
            display: none;
        }

        > .icon {
            margin-top: 6px;
        }
    }
    > label:first-of-type {
        border-top: 1px solid var(--content-color-40);
    }
    > div {
        border: 1px solid var(--content-color-40);
        border-top: none;
        display: none;
        padding: 10px;
    }
    > input[type='radio']:checked + label {
        background-color: var(--content-color-20);
        transition: background-color var(--transition-duration);
        > .arrow {
            display: none;
        }
        > .check {
            display: inline-block;
        }
    }
    > input[type='radio']:checked + label + div {
        display: block;
        > * {
            animation-duration: var(--transition-duration-slow);
            animation-name: terms_of_use_fadein;
        }
    }
}
</style>
