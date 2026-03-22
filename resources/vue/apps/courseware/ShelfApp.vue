<template>
    <div v-if="context">
        <div class="cw-shelf">
            <CoursewareUnitItems />
            <CoursewareSharedItems v-if="!inCourseContext" />
        </div>
        <courseware-shelf-dialog-add-chooser v-if="showUnitAddDialog" />
        <courseware-shelf-dialog-add v-if="showUnitNewDialog" />
        <courseware-shelf-dialog-copy v-if="showUnitCopyDialog" />
        <courseware-shelf-dialog-import v-if="showUnitImportDialog" />
        <courseware-shelf-dialog-topics v-if="showUnitTopicsDialog" />
        <Teleport v-if="userIsTeacher || !inCourseContext" to="#courseware-action-widget" name="sidebar-actions">
            <courseware-shelf-action-widget></courseware-shelf-action-widget>
        </Teleport>
        <courseware-companion-overlay />
    </div>
</template>

<script>
import CoursewareShelfActionWidget from '@/vue/components/courseware/widgets/CoursewareShelfActionWidget.vue';
import CoursewareShelfDialogAdd from '@/vue/components/courseware/unit/CoursewareShelfDialogAdd.vue';
import CoursewareShelfDialogAddChooser from '@/vue/components/courseware/unit/CoursewareShelfDialogAddChooser.vue';
import CoursewareShelfDialogCopy from '@/vue/components/courseware/unit/CoursewareShelfDialogCopy.vue';
import CoursewareShelfDialogImport from '@/vue/components/courseware/unit/CoursewareShelfDialogImport.vue';
import CoursewareShelfDialogTopics from '@/vue/components/courseware/unit/CoursewareShelfDialogTopics.vue';
import CoursewareUnitItems from '@/vue/components/courseware/unit/CoursewareUnitItems.vue';
import CoursewareSharedItems from '@/vue/components/courseware/unit/CoursewareSharedItems.vue';
import CoursewareCompanionOverlay from '@/vue/components/courseware/layouts/CoursewareCompanionOverlay.vue';

import { mapGetters } from 'vuex';

export default {
    components: {
        CoursewareShelfActionWidget,
        CoursewareShelfDialogAdd,
        CoursewareShelfDialogAddChooser,
        CoursewareShelfDialogCopy,
        CoursewareShelfDialogImport,
        CoursewareShelfDialogTopics,
        CoursewareUnitItems,
        CoursewareSharedItems,
        CoursewareCompanionOverlay,
    },
    data() {
        return {
            rate: 0,
        };
    },
    computed: {
        ...mapGetters({
            showUnitAddDialog: 'showUnitAddDialog',
            showUnitCopyDialog: 'showUnitCopyDialog',
            showUnitImportDialog: 'showUnitImportDialog',
            showUnitLinkDialog: 'showUnitLinkDialog',
            showUnitNewDialog: 'showUnitNewDialog',
            showUnitTopicsDialog: 'showUnitTopicsDialog',
            licenses: 'licenses',
            context: 'context',
            userIsTeacher: 'userIsTeacher',
            userId: 'userId',
        }),
        inCourseContext() {
            return this.context?.type === 'courses';
        },
    },
    async beforeCreate() {
        STUDIP.loadChunk('courseware');
        this.$store.dispatch('setUrlHelper', STUDIP.URLHelper);
        this.$store.dispatch('setUserId', STUDIP.USER_ID);
        this.$store.dispatch('users/loadById', { id: STUDIP.USER_ID });

        const { id, type } = this.$store.getters.context;
        const feedbackSettings = this.$store.getters.feedbackSettings;
        if (type === 'courses') {
            const isTeacher = this.$store.getters.userIsTeacher
            this.$store.dispatch('setUserIsTeacher', isTeacher);
            await this.$store.dispatch('loadCourseUnits', id);
            await this.$store.dispatch('setFeedbackSettings', feedbackSettings);
        } else {
            await this.$store.dispatch('loadUserUnits', id);
            await this.$store.dispatch('courseware-structural-elements-shared/loadAll', { options: { include: 'owner' } });
        }
    },
};
</script>
