<template>
    <div class="cw-panel-tasks">
        <h2>{{ $gettext('Aufgaben') }}</h2>
        <ul>
            <li>
                <StudipIcon class="cw-tasks-icon" shape="category-task" :size="32" />
                <a class="cw-tasks-title" :href="taskLink">
                    <h3>{{ $gettext('Zu den Aufgaben') }}</h3>
                    <p>{{ $gettext('Übersicht über alle Aufgaben in derser Veranstaltung') }}</p>
                </a>
            </li>
            <li>
                <StudipIcon class="cw-tasks-icon" shape="date" role="status-green" :size="32" />
                <a class="cw-tasks-title" href="#">
                    <h3>Aufgabe 1</h3>
                    <p>Lernmaterial C</p>
                </a>
            </li>
        </ul>
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