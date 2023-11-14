<template>
    <StudipDialog
        v-if="show && process"
        :title="$gettext('Zuordnungen festlegen')"
        :confirmText="$gettext('Speichern')"
        confirmClass="accept"
        :closeText="$gettext('Schließen')"
        closeClass="cancel"
        height="800"
        width="800"
        @close="onClose"
        @confirm="onConfirm"
    >
        <template #dialogContent>
            <PairingEditor v-if="pairings" v-model="pairings" :solvers="solvers" />
        </template>
    </StudipDialog>
</template>

<script>
import { mapGetters } from 'vuex';
import PairingEditor from './PairingEditor.vue';
import StudipDialog from '../../../StudipDialog.vue';

const objId = ({ id, type }) => ({ id, type });

export default {
    model: {
        prop: 'show',
        event: 'updateShow',
    },
    components: {
        PairingEditor,
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
        return {
            pairings: [],
        };
    },
    computed: {
        ...mapGetters({
            relatedPeerReviews: 'courseware-peer-reviews/related',
            relatedTaskGroups: 'courseware-task-groups/related',
        }),
        reviewPairs() {
            return this.relatedPeerReviews({ parent: this.process, relationship: 'peer-reviews' }).map((review) => ({
                reviewer: this.getObject(review.relationships.reviewer.data),
                submitter: this.getObject(review.relationships.submitter.data),
            }));
        },
        solvers() {
            return this.taskGroup.relationships.solvers.data.map((solver) => this.getObject(solver));
        },
        taskGroup() {
            return this.relatedTaskGroups({ parent: this.process, relationship: 'task-group' });
        },
    },
    methods: {
        getObject({ type, id }) {
            return this.$store.getters[`${type}/byId`]({ id });
        },
        onClose() {
            this.$emit('updateShow', false);
        },
        onConfirm() {
            this.$emit('update', this.pairings);
        },
    },
    watch: {
        show() {
            if (this.show) {
                this.pairings = this.reviewPairs;
            }
        },
    },
};
</script>
