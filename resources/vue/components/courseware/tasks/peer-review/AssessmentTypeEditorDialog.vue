<template>
    <StudipDialog
        v-if="show && process"
        :title="$gettext('Peer-Review-Form ändern')"
        :confirmText="$gettext('Speichern')"
        confirmClass="accept"
        :closeText="$gettext('Schließen')"
        closeClass="cancel"
        height="420"
        width="800"
        @close="onClose"
        @confirm="onConfirm"
    >
        <template #dialogContent>
            <component v-bind:is="editorComponent" v-model="payload"></component>
        </template>
    </StudipDialog>
</template>

<script>
import { mapGetters } from 'vuex';
import EditorForm from './assessment-types/editors/EditorForm.vue';
import EditorTable from './assessment-types/editors/EditorTable.vue';
import StudipDialog from '../../../StudipDialog.vue';
import { ASSESSMENT_TYPES } from './process-configuration';

const getConfiguration = (process) => process?.attributes?.configuration ?? {};
const getPayload = (process) => {
    const configuration = getConfiguration(process);
    const defaultPayload = ASSESSMENT_TYPES[configuration.type].defaultPayload ?? {};
    return _.isEmpty(configuration.payload) ? defaultPayload : configuration.payload;
};

export default {
    model: {
        prop: 'show',
        event: 'updateShow',
    },
    components: {
        StudipDialog,
    },
    props: {
        show: {
            type: Boolean,
            required: true,
        },
        process: {
            type: Object,
            default: null,
        },
    },
    data() {
        return { localPayload: _.cloneDeep(getPayload(this.process)) };
    },
    computed: {
        ...mapGetters({}),
        editorComponent() {
            switch (getConfiguration(this.process)?.type) {
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
                return getPayload(this.process);
            },
            set(payload) {
                this.localPayload = payload;
            },
        },
    },
    methods: {
        onClose() {
            this.$emit('updateShow', false);
        },
        onConfirm(...args) {
            this.$emit('update', _.cloneDeep(this.localPayload));
        },
    },
};
</script>
