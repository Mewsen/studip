<template>
    <div>
        <StudipArticle>
            <template #title> {{ $gettext('Peer-Review-Verfahren') }} </template>
            <template #body>
                <CompanionBox
                    v-if="!hasPeerReviewProcesses"
                    mood="pointing"
                    :msgCompanion="$gettext('Für diese Aufgabe wurde noch kein Peer-Review-Verfahren aktiviert.')"
                >
                    <template #companionActions>
                        <button class="button" @click="$emit('add-peer-review-process')">
                            {{ $gettext('Peer-Review-Verfahren aktivieren') }}
                        </button>
                    </template>
                </CompanionBox>
                <ProcessDetail
                    v-for="process in peerReviewProcesses"
                    :key="process.id"
                    :process="process"
                    @show-assessment-type-editor="onShowAssessmentTypeEditor(process)"
                    @show-pairing-editor="onShowPairingEditor(process)"
                    @change-peer-review-process-duration="onShowPeerReviewProcessDuration(process)"
                    @edit-peer-review-process="onShowPeerReviewProcessEdit(process)"
                />
            </template>
        </StudipArticle>

        <AssessmentTypeEditorDialog
            v-if="showAssessmentTypeEditor"
            v-model="showAssessmentTypeEditor"
            :process="selectedProcess"
            @update="onUpdateAssessmentType"
        />
        <PairingEditorDialog v-model="showPairingEditor" :process="selectedProcess" @update="onUpdatePairing" />
        <ProcessEditDialog
            v-if="showPeerReviewProcessEdit"
            :process="selectedProcess"
            @update="onUpdatePeerReviewProcess"
            @close="showPeerReviewProcessEdit = false"
        />
        <ProcessDurationDialog
            v-model="showPeerReviewProcessDuration"
            :process="selectedProcess"
            @update="onUpdateDuration"
        />
    </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex';
import AssessmentTypeEditorDialog from './peer-review/AssessmentTypeEditorDialog.vue';
import CompanionBox from '../layouts/CoursewareCompanionBox.vue';
import PairingEditorDialog from './peer-review/PairingEditorDialog.vue';
import ProcessDetail from './peer-review/ProcessDetail.vue';
import ProcessDurationDialog from './peer-review/ProcessDurationDialog.vue';
import ProcessEditDialog from './peer-review/ProcessEditDialog.vue';
import StudipArticle from '../../StudipArticle.vue';
import { getStatus } from './task-groups-helper.js';

export default {
    components: {
        AssessmentTypeEditorDialog,
        CompanionBox,
        PairingEditorDialog,
        ProcessDetail,
        ProcessDurationDialog,
        ProcessEditDialog,
        StudipArticle,
    },
    props: ['taskGroup'],
    data: () => ({
        selectedProcess: null,
        showAssessmentTypeEditor: false,
        showPairingEditor: false,
        showPeerReviewProcessDuration: false,
        showPeerReviewProcessEdit: false,
    }),
    computed: {
        ...mapGetters({
            relatedPeerReviewProcesses: 'courseware-peer-review-processes/related',
        }),
        hasPeerReviewProcesses() {
            return !!this.peerReviewProcesses;
        },
        isAfter() {
            return new Date() > new Date(this.taskGroup.attributes['end-date']);
        },
        peerReviewProcesses() {
            return this.relatedPeerReviewProcesses({ parent: this.taskGroup, relationship: 'peer-review-processes' });
        },
    },
    methods: {
        ...mapActions({
            loadRelatedPeerReviews: 'courseware-peer-reviews/loadRelated',
            replacePairings: 'tasks/replacePairings',
            updatePeerReviewProcess: 'tasks/updatePeerReviewProcess',
        }),
        loadPeerReviews() {
            return this.loadRelatedPeerReviews({
                parent: this.process,
                relationship: 'peer-reviews',
                options: { include: 'reviewer,task' },
            });
        },
        onShowAssessmentTypeEditor(process) {
            this.selectedProcess = process;
            this.showAssessmentTypeEditor = true;
        },
        onShowPairingEditor(process) {
            this.selectedProcess = process;
            this.showPairingEditor = true;
        },
        onShowPeerReviewProcessDuration(process) {
            console.debug('change-peer-review-process-duration', process);
            this.selectedProcess = process;
            this.showPeerReviewProcessDuration = true;
        },
        onShowPeerReviewProcessEdit(process) {
            this.selectedProcess = process;
            this.showPeerReviewProcessEdit = true;
        },
        onUpdateAssessmentType(payload) {
            const configuration = this.selectedProcess.attributes.configuration;
            configuration.payload = payload;

            this.updatePeerReviewProcess({ process: this.selectedProcess, configuration }).then(() => {
                this.selectedProcess = null;
                this.showAssessmentTypeEditor = false;
            });
        },
        onUpdateDuration(duration) {
            const configuration = { ...this.selectedProcess.attributes.configuration, duration };
            this.updatePeerReviewProcess({ process: this.selectedProcess, configuration }).then(() => {
                this.selectedProcess = null;
                this.showPeerReviewProcessDuration = false;
            });
        },
        onUpdatePairing(pairings) {
            this.replacePairings({ process: this.selectedProcess, pairings })
                .then(() => this.loadPeerReviews())
                .then(() => {
                    this.selectedProcess = null;
                    this.showPairingEditor = false;
                })
                .catch((error) => {
                    console.error('Could not replace pairings.', error);
                });
        },
        onUpdatePeerReviewProcess({ configuration }) {
            this.updatePeerReviewProcess({ process: this.selectedProcess, configuration }).then(() => {
                this.selectedProcess = null;
                this.showPeerReviewProcessEdit = false;
            });
        },
    },
};
</script>
