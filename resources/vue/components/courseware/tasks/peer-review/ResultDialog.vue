<template>
    <StudipDialog
        v-if="show"
        :title="$gettext('Peer-Review ansehen')"
        :closeText="$gettext('Schließen')"
        closeClass="cancel"
        height="700"
        width="700"
        @close="onClose"
    >
        <template #dialogContent>
            <component v-bind:is="assessmentComponent" :process="process" :review="review"></component>
        </template>
    </StudipDialog>
</template>

<script>
import ResultForm from './assessment-types/results/Form.vue';
import ResultFreetext from './assessment-types/results/Freetext.vue';
import ResultTable from './assessment-types/results/Table.vue';
import StudipDialog from '../../../StudipDialog.vue';
import { mapActions, mapGetters } from 'vuex';

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
        review: {
            type: Object,
            required: true,
        },
    },
    computed: {
        ...mapGetters({
            relatedProcess: 'courseware-peer-review-processes/related',
        }),
        assessmentComponent() {
            switch (this.configuration?.type) {
                case 'form':
                    return ResultForm;
                case 'freetext':
                    return ResultFreetext;
                case 'table':
                    return ResultTable;
                default:
                    return null;
            }
        },
        configuration() {
            return this.process?.attributes?.configuration ?? {};
        },
        process() {
            return this.relatedProcess({
                parent: { id: this.review.id, type: this.review.type },
                relationship: 'process',
            });
        },
    },
    methods: {
        onClose() {
            this.$emit('updateShow', false);
        },
        onConfirm() {
            this.$emit('updateShow', false);
        },
    },
};
</script>
