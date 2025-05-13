<template>
    <div class="cw-admin">
        <courseware-admin-templates v-if="templatesView" />
        <Teleport to="#courseware-admin-view-widget" name="sidebar-views">
            <courseware-admin-view-widget />
        </Teleport>
        <Teleport to="#courseware-admin-action-widget" name="sidebar-views">
            <courseware-admin-action-widget />
        </Teleport>
    </div>
</template>

<script>
import { mapGetters } from 'vuex';
import CoursewareAdminActionWidget from '@/vue/components/courseware/widgets/CoursewareAdminActionWidget.vue';
import CoursewareAdminTemplates from '@/vue/components/courseware/CoursewareAdminTemplates.vue';
import CoursewareAdminViewWidget from '@/vue/components/courseware/widgets/CoursewareAdminViewWidget.vue';

export default {
    components: {
        CoursewareAdminActionWidget,
        CoursewareAdminTemplates,
        CoursewareAdminViewWidget,
    },
    computed: {
        ...mapGetters({
            adminViewMode: 'adminViewMode',
        }),
        templatesView() {
            return this.adminViewMode === 'templates';
        },
    },
    beforeCreate() {
        STUDIP.loadChunk('courseware');
        this.$store.dispatch('courseware-templates/loadAll');
    },
};
</script>
