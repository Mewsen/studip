<template>
    <StudipDialog
        v-if="show"
        :title="$gettext('Peer-Review verfassen')"
        :confirmText="isActive ? $gettext('Speichern') : ''"
        confirmClass="accept"
        :closeText="$gettext('Abbrechen')"
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
                        'Der Peer-Review-Prozess ist abgeschlossen. Sie können das Peer-Review nicht mehr ändern.'
                    )
                "
            />
            <component
                v-bind:is="assessmentComponent"
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
import ResultsTypeForm from './assessment-types/results/Form.vue';
import ResultsTypeFreetext from './assessment-types/results/Freetext.vue';
import ResultsTypeTable from './assessment-types/results/Table.vue';
import { getProcessStatus, ProcessStatus } from './definitions.ts';
import CompanionBox from '../../layouts/CoursewareCompanionBox.vue';
import StudipDialog from '../../../StudipDialog.vue';
import { mapActions, mapGetters } from 'vuex';

export default {
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
    emits: ['update:show'],
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
                    return this.isActive ? AssessmentTypeForm : ResultsTypeForm;
                case 'freetext':
                    return this.isActive ? AssessmentTypeFreetext : ResultsTypeFreetext;
                case 'table':
                    return this.isActive ? AssessmentTypeTable : ResultsTypeTable;
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
            this.$emit('update:show', false);
            this.assessment = {};
        },
        onConfirm() {
            this.$emit('update:show', false);
            this.storeAssessment({ review: this.review, assessment: this.assessment });
            this.globalEmit('push-system-notification', {
                type: 'success',
                message: this.$gettext('Peer-Review gespeichert.'),
            });
        },
    },
};
</script>
