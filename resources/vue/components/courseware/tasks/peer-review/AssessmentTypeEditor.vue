<template>
    <component v-if="editorComponent" v-bind:is="editorComponent" v-model:payload="payload"></component>
    <CompanionBox v-else :msgCompanion="$gettext('Dieses Bewertungssystem kann nicht konfiguriert werden.')" />
</template>

<script>
import { mapGetters } from 'vuex';
import CompanionBox from '../../layouts/CoursewareCompanionBox.vue';
import EditorForm from './assessment-types/editors/EditorForm.vue';
import EditorTable from './assessment-types/editors/EditorTable.vue';
import { ASSESSMENT_TYPES } from './process-configuration';

const getPayload = (configuration) => {
    const defaultPayload = ASSESSMENT_TYPES[configuration.type].defaultPayload ?? {};
    return _.isEmpty(configuration.payload) ? defaultPayload : configuration.payload;
};

const withPayload = (configuration, payload) => {
    return { ...configuration, payload };
};

export default {
    props: {
        configuration: {
            type: Object,
            default: () => ({}),
        },
    },
    components: { CompanionBox },
    data() {
        return { localPayload: _.cloneDeep(getPayload(this.configuration)) };
    },
    computed: {
        ...mapGetters({}),
        editorComponent() {
            switch (this.configuration?.type) {
                case 'form':
                    return EditorForm;
                case 'freetext':
                    return null;
                case 'table':
                    return EditorTable;
                default:
                    return null;
            }
        },
        payload: {
            get() {
                return getPayload(this.configuration);
            },
            set(payload) {
                this.updatePayload(payload);
            },
        },
    },
    methods: {
        updatePayload(payload) {
            if (!_.isEqual(this.localPayload, payload)) {
                this.localPayload = payload;
                this.$emit('update', withPayload(this.configuration, this.localPayload));
            }
        },
    },
};
</script>
