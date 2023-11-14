<template>
    <StudipDialog
        v-if="show"
        :title="$gettext('Peer-Review verfassen')"
        :confirmText="$gettext('Speichern')"
        confirmClass="accept"
        :confirmDisabled="!isActive"
        :closeText="$gettext('Schließen')"
        closeClass="cancel"
        height="700"
        width="700"
        @close="onClose"
        @confirm="onConfirm"
    >
        <template #dialogContent>
            <CompanionBox
                v-if="!isActive"
                mood="sad"
                :msgCompanion="
                    $gettext(
                        'Das Peer-Review-Verfahren ist abgeschlossen. Sie können das Peer-Review nicht mehr ändern.'
                    )
                "
            />
            <component
                v-bind:is="assessmentComponent"
                :disabled="!isActive"
                :process="process"
                :review="review"
                @answer="onAnswer"
            ></component>
        </template>
    </StudipDialog>
</template>

<script>
import AssessmentTypeForm from './assessment-types/forms/AssessmentTypeForm.vue';
import AssessmentTypeFreetext from './assessment-types/forms/AssessmentTypeFreetext.vue';
import AssessmentTypeTable from './assessment-types/forms/AssessmentTypeTable.vue';
import { getProcessStatus, ProcessStatus } from './definitions.ts';
import CompanionBox from '../../layouts/CoursewareCompanionBox.vue';
import StudipDialog from '../../../StudipDialog.vue';
import { mapActions, mapGetters } from 'vuex';

export default {
    model: {
        prop: 'show',
        event: 'updateShow',
    },
    components: {
        CompanionBox,
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
    data: () => ({
        assessment: {},
    }),
    computed: {
        ...mapGetters({
            relatedProcess: 'courseware-peer-review-processes/related',
        }),
        assessmentComponent() {
            switch (this.configuration?.type) {
                case 'form':
                    return AssessmentTypeForm;
                case 'freetext':
                    return AssessmentTypeFreetext;
                case 'table':
                    return AssessmentTypeTable;
                default:
                    return null;
            }
        },
        configuration() {
            return this.process?.attributes?.configuration ?? {};
        },
        isActive() {
            return this.process && getProcessStatus(this.process)?.status === ProcessStatus.Active;
        },
        process() {
            return this.relatedProcess({
                parent: { id: this.review.id, type: this.review.type },
                relationship: 'process',
            });
        },
    },
    methods: {
        ...mapActions({
            storeAssessment: 'tasks/storeAssessment',
        }),
        onAnswer(assessment) {
            this.assessment = assessment;
        },
        onClose() {
            this.$emit('updateShow', false);
            this.assessment = {};
        },
        onConfirm() {
            this.$emit('updateShow', false);
            this.storeAssessment({ review: this.review, assessment: this.assessment });
        },
    },
};
</script>
