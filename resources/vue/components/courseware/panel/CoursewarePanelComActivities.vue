<template>
    <div>
        <h2>{{ $gettext('Aktivitäten') }}</h2>
        <CoursewareActivities />
    </div>
</template>
<script>
import CoursewareActivities from '../CoursewareActivities.vue';
export default {
    components: {
        CoursewareActivities,
    },
};
</script>
