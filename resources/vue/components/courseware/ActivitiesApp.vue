<template>
    <div class="cw-activities-wrapper">
        <courseware-activities />
        <Teleport to="#courseware-activities-widget-filter-type" name="sidebar-filter-type">
            <courseware-activities-widget-filter-type />
        </Teleport>
        <Teleport to="#courseware-activities-widget-filter-unit" name="sidebar-filter-unit">
            <courseware-activities-widget-filter-unit />
        </Teleport>
    </div>
</template>

<script>
import CoursewareActivities from './CoursewareActivities.vue';
import CoursewareActivitiesWidgetFilterType from './widgets/CoursewareActivitiesWidgetFilterType.vue';
import CoursewareActivitiesWidgetFilterUnit from './widgets/CoursewareActivitiesWidgetFilterUnit.vue';

export default {
    components: {
        CoursewareActivities,
        CoursewareActivitiesWidgetFilterType,
        CoursewareActivitiesWidgetFilterUnit
    }
}
</script>
