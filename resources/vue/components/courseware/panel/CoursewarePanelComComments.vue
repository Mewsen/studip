<template>
    <div v-if="commentsLoaded" class="cw-panel-com-comments">
        <h2>{{ $gettext('Kommentare und Anmerkungen') }}</h2>
        <CoursewareCommentsBlocks />
        <CoursewareCommentsStructuralElements />
    </div>
</template>

<script>
import CoursewareCommentsBlocks from './CoursewareCommentsBlocks.vue';
import CoursewareCommentsStructuralElements from './CoursewareCommentsStructuralElements.vue';
import axios from 'axios';

export default {
    components: {
        CoursewareCommentsBlocks,
        CoursewareCommentsStructuralElements,
    },
    data() {
        return {
            commentsLoaded: false
        }
    },
    async mounted() {
        const data = await axios(STUDIP.URLHelper.getURL('dispatch.php/course/courseware/comments_overview_data/'));

        this.$store.commit('courseware-structural-elements/STORE_RECORDS', JSON.parse(data.data['elements']).data, {
            root: true,
        });
        this.$store.commit('courseware-containers/STORE_RECORDS', JSON.parse(data.data['containers']).data, {
            root: true,
        });
        this.$store.commit('courseware-blocks/STORE_RECORDS', JSON.parse(data.data['blocks']).data, { root: true });
        this.$store.commit('courseware-block-comments/STORE_RECORDS', JSON.parse(data.data['block_comments']).data, {
            root: true,
        });
        this.$store.commit('courseware-block-feedback/STORE_RECORDS', JSON.parse(data.data['block_feedbacks']).data, {
            root: true,
        });
        this.$store.commit(
            'courseware-structural-element-comments/STORE_RECORDS',
            JSON.parse(data.data['element_comments']).data,
            { root: true }
        );
        this.$store.commit(
            'courseware-structural-element-feedback/STORE_RECORDS',
            JSON.parse(data.data['element_feedbacks']).data,
            { root: true }
        );

        this.$nextTick(() => {
            this.commentsLoaded = true;
        });
    },
};
</script>
