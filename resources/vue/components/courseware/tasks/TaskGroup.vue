<template>
    <div>
        <section v-if="tasks.length > 0">
            <table class="default">
                <caption>
                    {{
                        $gettext('Verteilte Aufgaben')
                    }}
                </caption>
                <thead>
                    <tr>
                        <th>{{ $gettext('Status') }}</th>
                        <th>{{ $gettext('Teilnehmende/Gruppen') }}</th>
                        <th class="responsive-hidden">{{ $gettext('Seite') }}</th>
                        <th>{{ $gettext('bearbeitet') }}</th>
                        <th>{{ $gettext('Abgabefrist') }}</th>
                        <th>{{ $gettext('Abgabe') }}</th>
                        <th class="responsive-hidden renewal">{{ $gettext('Verlängerungsanfrage') }}</th>
                        <th class="responsive-hidden feedback">{{ $gettext('Feedback') }}</th>
                    </tr>
                </thead>
                <tbody>
                    <TaskItem
                        v-for="task in tasks"
                        :task="task"
                        :taskGroup="taskGroup"
                        :key="task.id"
                        @add-feedback="(task) => $emit('add-feedback', task)"
                        @edit-feedback="(feedback) => $emit('edit-feedback', feedback)"
                        @solve-renewal="(task) => $emit('solve-renewal', task)"
                    />
                </tbody>
            </table>

            <PeerReviewProcesses
                :taskGroup="taskGroup"
                @add-peer-review-process="$emit('add-peer-review-process', taskGroup)"
                class="cw-task-group-peer-review-processes"
            />
        </section>
        <div v-else>
            <CompanionBox mood="pointing" :msgCompanion="$gettext('Diese Aufgabe wurde an niemanden verteilt.')" />
        </div>
    </div>
</template>

<script>
import { mapGetters } from 'vuex';
import CompanionBox from '../layouts/CoursewareCompanionBox.vue';
import PeerReviewProcesses from './TaskGroupPeerReviewProcesses.vue';
import TaskItem from './TaskGroupTaskItem.vue';

export default {
    components: { CompanionBox, PeerReviewProcesses, TaskItem },
    emits: ['add-feedback', 'edit-feedback', 'solve-renewal'],
    props: ['taskGroup', 'tasks'],
    computed: {
        ...mapGetters({
            coursewareContext: 'context',
        }),
        actionMenuContext() {
            return this.$gettext(
                'Courseware-Aufgabe "%{ taskGroup }"',
                { taskGroup: this.taskGroup.attributes.title }
            );
        },
    },
};
</script>
