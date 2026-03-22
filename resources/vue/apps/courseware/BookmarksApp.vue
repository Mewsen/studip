<template>
    <div class="cw-content-bookmark">
        <CoursewareContentBookmarks />
        <Teleport to="#courseware-content-bookmark-filter-widget" name="sidebar-views">
            <CoursewareContentBookmarkFilterWidget />
        </Teleport>
    </div>
</template>

<script>
import CoursewareContentBookmarks from '@/vue/components/courseware/CoursewareContentBookmarks.vue';
import CoursewareContentBookmarkFilterWidget from '@/vue/components/courseware/CoursewareContentBookmarkFilterWidget.vue';

export default {
    components: {
        CoursewareContentBookmarks,
        CoursewareContentBookmarkFilterWidget,
    },

    async beforeCreate() {
        STUDIP.loadChunk('courseware');

        this.$store.dispatch('setUserId', STUDIP.USER_ID);
        this.$store.dispatch('users/loadById', { id: STUDIP.USER_ID });
        this.$store.dispatch('loadUsersBookmarks', STUDIP.USER_ID);
    },
};
</script>
