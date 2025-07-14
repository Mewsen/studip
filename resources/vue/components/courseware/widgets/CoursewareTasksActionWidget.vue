<template>
    <sidebar-widget id="courseware-action-widget" :title="$gettext('Aktionen')">
        <template #content>
            <ul class="widget-list widget-links cw-action-widget">
                <template v-if="taskGroup">
                    <li v-if="isBeforeEndDate" class="cw-action-widget-date">
                        <button @click="modifyDeadline(taskGroup)">
                            {{ $gettext('Bearbeitungszeit verlängern') }}
                        </button>
                    </li>
                    <li v-if="isBeforeEndDate" class="cw-action-widget-add">
                        <button @click="addSolvers(taskGroup)">
                            {{ $gettext('Teilnehmende hinzufügen') }}
                        </button>
                    </li>
                    <li class="cw-action-widget-delete">
                        <button @click="deleteTaskGroup(taskGroup)">
                            {{ $gettext('Aufgabe löschen') }}
                        </button>
                    </li>
                </template>
                <li v-else class="cw-action-widget-add">
                    <button @click="setShowTasksDistributeDialog(true)">
                        {{ $gettext('Aufgabe verteilen') }}
                    </button>
                </li>
                <li v-if="taskGroup && !hasPeerReviewProcesses" class="cw-action-widget-play">
                    <button @click="$emit('add-peer-review-process')">
                        {{ $gettext('Peer-Review-Verfahren aktivieren') }}
                    </button>
                </li>

            </ul>
        </template>
    </sidebar-widget>
</template>

<script>
import SidebarWidget from '../../SidebarWidget.vue';

import { mapActions } from 'vuex';

export default {
    name: 'courseware-tasks-action-widget',
    components: {
        SidebarWidget,
    },
    props: ['hasPeerReviewProcesses', 'taskGroup'],
    computed: {
        isBeforeEndDate() {
            return this.taskGroup && new Date() < new Date(this.taskGroup.attributes['end-date']);
        },
    },
    methods: {
        ...mapActions({
            addSolvers: 'tasks/setShowTaskGroupsAddSolversDialog',
            deleteTaskGroup: 'tasks/setShowTaskGroupsDeleteDialog',
            modifyDeadline: 'tasks/setShowTaskGroupsModifyDeadlineDialog',
            setShowTasksDistributeDialog: 'tasks/setShowTasksDistributeDialog',
        }),
    },
};
</script>