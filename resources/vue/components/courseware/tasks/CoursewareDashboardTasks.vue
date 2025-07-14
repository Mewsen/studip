<template>
    <div class="courseware-dashboard-tasks">
        <TasksList />
        <ProcessesList />
    </div>
</template>
<script>
import TasksList from './CoursewareDashboardTasksList.vue';
import ProcessesList from './peer-review/ProcessesList.vue';

export default {
    components: { ProcessesList, TasksList },
};
</script>