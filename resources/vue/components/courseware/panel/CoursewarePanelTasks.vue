<template>
    <div class="cw-panel-tasks">
        <a class="button" :href="taskLink">{{ $gettext('Zu den Aufgaben') }}</a>
    </div>
</template>

<script>
export default {
    name: 'CoursewarePanelTasks',
    computed: {
        taskLink() {
            return STUDIP.URLHelper.getURL('dispatch.php/course/courseware/tasks/');
        }
    }
}
</script>