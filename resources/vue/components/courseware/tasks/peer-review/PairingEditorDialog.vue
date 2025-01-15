<template>
    <StudipDialog
        v-if="show && process"
        :title="$gettext('Zuordnungen festlegen')"
        :confirmText="$gettext('Speichern')"
        confirmClass="accept"
        :confirmDisabled="!pairings?.length"
        :closeText="$gettext('Schließen')"
        closeClass="cancel"
        height="800"
        width="800"
        @close="onClose"
        @confirm="onConfirm"
    >
        <template #dialogContent>
            <PairingEditor v-if="!storing && pairings" v-model:pairings="pairings" :solvers="solvers" />
            <ProgressIndicator v-if="storing" :description="$gettext('Zuordnungen werden gespeichert …')" />
        </template>
    </StudipDialog>
</template>

<script>
import { mapGetters } from 'vuex';
import PairingEditor from './PairingEditor.vue';
import StudipDialog from '../../../StudipDialog.vue';
import ProgressIndicator from '../../../StudipProgressIndicator.vue';

const objId = ({ id, type }) => ({ id, type });

export default {
    components: {
        PairingEditor,
        ProgressIndicator,
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
    emits: ['update:show', 'update'],
    data() {
        return {
            pairings: [],
            storing: false,
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
            this.$emit('update:show', false);
        },
        onConfirm() {
            if (!this.storing) {
                this.storing = true;
                this.$emit('update', this.pairings);
            }
        },
        resetLocalState() {
            this.storing = false;
        },
    },
    mounted() {
        this.resetLocalState();
    },
    updated() {
        this.resetLocalState();
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
