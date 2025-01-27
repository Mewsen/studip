<template>
  <div class="cw-content-bookmark">
        <courseware-content-bookmarks />
        <Teleport to="#courseware-content-bookmark-filter-widget" name="sidebar-views">
            <courseware-content-bookmark-filter-widget />
        </Teleport>
  </div>
</template>

<script>
import CoursewareContentBookmarks from './CoursewareContentBookmarks.vue';
import CoursewareContentBookmarkFilterWidget from './CoursewareContentBookmarkFilterWidget.vue';

export default {
    components: {
        CoursewareContentBookmarks,
        CoursewareContentBookmarkFilterWidget
    },

}
</script>
