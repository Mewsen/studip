<template>
    <div class="cw-comments-overview-wrapper">
        <courseware-block-comments-overview v-if="showBlocks"/>
        <courseware-structural-element-comments-overview v-if="showElements"/>
        <Teleport to="#courseware-comments-overview-widget-filter-type" name="sidebar-views">
            <courseware-comments-overview-widget-filter-type />
        </Teleport>
        <Teleport to="#courseware-comments-overview-widget-filter-created" name="sidebar-views">
            <courseware-comments-overview-widget-filter-created />
        </Teleport>
        <Teleport to="#courseware-comments-overview-widget-filter-unit" name="sidebar-views">
            <courseware-comments-overview-widget-filter-unit />
        </Teleport>
    </div>
</template>

<script>
import CoursewareBlockCommentsOverview from './CoursewareBlockCommentsOverview.vue';
import CoursewareStructuralElementCommentsOverview from './CoursewareStructuralElementCommentsOverview.vue';
import CoursewareCommentsOverviewWidgetFilterType from './widgets/CoursewareCommentsOverviewWidgetFilterType.vue';
import CoursewareCommentsOverviewWidgetFilterCreated from './widgets/CoursewareCommentsOverviewWidgetFilterCreated.vue';
import CoursewareCommentsOverviewWidgetFilterUnit from './widgets/CoursewareCommentsOverviewWidgetFilterUnit.vue';

import { mapGetters } from 'vuex';

export default {
    components: {
        CoursewareBlockCommentsOverview,
        CoursewareStructuralElementCommentsOverview,
        CoursewareCommentsOverviewWidgetFilterType,
        CoursewareCommentsOverviewWidgetFilterCreated,
        CoursewareCommentsOverviewWidgetFilterUnit,
    },
    computed: {
        ...mapGetters({
            typeFilter: 'typeFilter',
        }),
        showBlocks() {
            return this.typeFilter === 'blocks' || this.typeFilter === 'all';
        },
        showElements() {
            return this.typeFilter === 'elements' || this.typeFilter === 'all';
        }
    }
}
</script>