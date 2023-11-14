<template>
    <div>
        <CompanionBox
            v-if="isActive"
            :msgCompanion="
                $gettext(
                    'Der Peer-Review-Prozess hat bereits begonnen. Die Einstellungen können Bis auf die Bearbeitungsdauer nicht geändert werden.'
                )
            "
        />

        <section>
            <article>
                <header>
                    <h4>{{ $gettext('Status') }}</h4>
                </header>
                <div class="cw-peer-review-processes-status">
                    <ProcessStatus :process="process" />
                    <span>{{ processStatus.description }}</span>
                </div>
                <div>
                    <span>{{ $gettext('Bearbeitungszeit:') }}</span>
                    <StudipDate :date="startDate" />–<StudipDate :date="endDate" />
                </div>
                <div v-if="canChangeDurationOnly">
                    <button class="button" @click="$emit('change-peer-review-process-duration')">
                        {{ $gettext('Bearbeitungszeit verlängern') }}
                    </button>
                </div>
            </article>
            <article>
                <header>
                    <h4>{{ $gettext('Einstellungen') }}</h4>
                </header>
                <div>
                    <ProcessConfiguration :options="configuration" />
                </div>
                <div>
                    <button
                        class="button"
                        @click="$emit('edit-peer-review-process')"
                        :disabled="!canChangeConfiguration"
                    >
                        {{ $gettext('Einstellungen ändern') }}
                    </button>
                    <button
                        v-if="configuration.type === 'form' || configuration.type === 'table'"
                        class="button"
                        @click="$emit('show-assessment-type-editor')"
                        :disabled="!canChangeConfiguration"
                    >
                        {{ $gettext('Bewertungssystem konfigurieren') }}
                    </button>
                </div>
            </article>

            <article>
                <header>
                    <h4>{{ $gettext('Peer-Reviews') }}</h4>
                </header>
                <div>
                    <template v-if="isBefore">
                        <CompanionBox
                            v-if="isAutomaticPairing"
                            :msgCompanion="
                                $gettext(
                                    'In diesem Peer-Review-Prozess werden die Paarungen automatisch verteilt, sobald der Bearbeitungszeitraum beginnt.'
                                )
                            "
                        >
                        </CompanionBox>
                        <CompanionBox
                            v-else
                            mood="pointing"
                            :msgCompanion="
                                $gettext(
                                    'In diesem Peer-Review-Prozess werden die Paarungen manuell verteilt, bevor der Bearbeitungszeitraum beginnt.'
                                )
                            "
                        >
                            <template #companionActions>
                                <button class="button" @click="$emit('show-pairing-editor')">
                                    {{ $gettext('Paarungen manuell festlegen') }}
                                </button>
                            </template>
                        </CompanionBox>
                    </template>
                    <PeerReviewList :process="process" :task-group="taskGroup" />
                </div>
            </article>
        </section>
    </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex';
import CompanionBox from '../../layouts/CoursewareCompanionBox.vue';
import PeerReviewList from './PeerReviewList.vue';
import ProcessConfiguration from './ProcessConfiguration.vue';
import ProcessStatus from './ProcessStatus.vue';
import StudipDate from '../../../StudipDate.vue';
import { getProcessStatus, ProcessStatus as Status } from './definitions';

export default {
    components: {
        CompanionBox,
        PeerReviewList,
        ProcessConfiguration,
        ProcessStatus,
        StudipDate,
    },
    props: {
        process: {
            type: Object,
            required: true,
        },
    },
    emits: [
        'show-assessment-type-editor',
        'show-pairing-editor',
        'change-peer-review-process-duration',
        'edit-peer-review-process',
    ],
    data: () => ({}),
    computed: {
        ...mapGetters({
            getProcess: 'courseware-peer-review-processes/byId',
            relatedPeerReviews: 'courseware-peer-reviews/related',
            relatedTasks: 'courseware-tasks/related',
            relatedTaskGroups: 'courseware-task-groups/related',
            relatedUsers: 'users/related',
            userIsTeacher: 'userIsTeacher',
        }),
        canChangeConfiguration() {
            return this.isBefore;
        },
        canChangeDurationOnly() {
            return this.processStatus.status === Status.Active;
        },
        configuration() {
            return this.process.attributes['configuration'];
        },
        endDate() {
            return new Date(this.process.attributes['review-end']);
        },
        isActive() {
            return this.processStatus.status === Status.Active;
        },
        isAfter() {
            return this.processStatus.status === Status.After;
        },
        isBefore() {
            return this.processStatus.status === Status.Before;
        },
        isAutomaticPairing() {
            return this.configuration.automaticPairing;
        },
        owner() {
            return this.relatedUsers({ parent: this.process, relationship: 'owner' });
        },
        peerReviews() {
            const result = this.relatedPeerReviews({ parent: this.process, relationship: 'peer-reviews' });
            return result;
        },
        processStatus() {
            return getProcessStatus(this.process);
        },
        solvers() {
            return this.taskGroup.relationships.solvers.data.map(({ id, type }) => {
                return [id, type];
            });
        },
        startDate() {
            return new Date(this.process.attributes['review-start']);
        },
        taskGroup() {
            return this.relatedTaskGroups({ parent: this.process, relationship: 'task-group' });
        },
        tasks() {
            return this.relatedTasks({ parent: this.taskGroup, relationship: 'tasks' });
        },
    },
    methods: {
        ...mapActions({
            loadRelatedPeerReviews: 'courseware-peer-reviews/loadRelated',
        }),
        loadPeerReviews() {
            return this.loadRelatedPeerReviews({
                parent: this.process,
                relationship: 'peer-reviews',
                options: { include: 'reviewer,task' },
            });
        },
    },
    async mounted() {
        await this.loadPeerReviews();
    },
};
</script>

<style>
.cw-peer-review-processes-status {
    display: flex;
    gap: 0.25rem;
}
</style>
