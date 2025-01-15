<template>
    <div class="cw-peer-review-processes-wrapper" v-if="!userIsTeacher">
        <table class="default" v-if="peerReviews.length">
            <caption>
                {{ $gettext('Peer-Reviews') }}
            </caption>
            <thead>
                <tr>
                    <th>{{ $gettext('Status') }}</th>
                    <th>{{ $gettext('Bearbeitungszeit') }}</th>
                    <th>{{ $gettext('Aufgabe') }}</th>
                    <th>
                        {{ $gettext('Erhaltene Peer-Reviews') }}
                    </th>
                    <th>
                        {{ $gettext('Gegebene Peer-Reviews') }}
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="process in processes" :key="process.id">
                    <td>
                        <ProcessStatusIcon :process="process" />
                    </td>
                    <td>
                        <StudipDate :date="new Date(process.attributes['review-start'])" />
                        -
                        <StudipDate :date="new Date(process.attributes['review-end'])" />
                    </td>
                    <td>
                        {{ taskGroups[process.id].attributes.title }}
                    </td>
                    <td>
                        <div v-for="review in peerReviewsForMe(process)" :key="review.id">
                            <template v-if="isPeerReviewProcessAfter(process)">
                                <template v-if="review.attributes.assessment">
                                    <a :href="elementUrls[review.id]" class="button">
                                        {{ $gettext('Erhaltenes Peer-Review anzeigen') }}
                                    </a>
                                </template>
                            </template>
                            <template v-else>
                                <button class="button" disabled>
                                    {{ $gettext('Peer-Review noch nicht sichtbar') }}
                                </button>
                            </template>
                        </div>
                    </td>
                    <td>
                        <div v-for="review in peerReviewsFromMe(process)" :key="review.id">
                            <template v-if="isPeerReviewProcessActive(process)">
                                <a :href="elementUrls[review.id]" class="button">
                                    {{ $gettext('Peer-Review geben') }}
                                </a>
                            </template>
                            <template v-else-if="review.attributes.assessment">
                                <a :href="elementUrls[review.id]" class="button">
                                    {{ $gettext('Gegebenes Peer-Review anzeigen') }}
                                </a>
                            </template>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
        <CompanionBox
            v-else-if="!loading"
            mood="sad"
            :msgCompanion="$gettext('Sie haben noch keine Peer-Reviews erhalten oder gegeben.')"
        />
    </div>
</template>

<script>
import _ from 'lodash';
import { mapActions, mapGetters } from 'vuex';
import CompanionBox from '../../layouts/CoursewareCompanionBox.vue';
import ProcessStatusIcon from './ProcessStatus.vue';
import StudipDate from '../../../StudipDate.vue';
import taskHelper from '../../../../mixins/courseware/task-helper.js';
import { getProcessStatus, ProcessStatus } from './definitions';

export default {
    components: {
        CompanionBox,
        ProcessStatusIcon,
        StudipDate,
    },
    mixins: [taskHelper],
    data: () => ({
        loading: true,
    }),
    computed: {
        ...mapGetters({
            context: 'context',
            relatedPeerReviewProcesses: 'courseware-peer-review-processes/related',
            relatedPeerReviews: 'courseware-peer-reviews/related',
            relatedStructuralElement: 'courseware-structural-elements/related',
            relatedTask: 'courseware-tasks/related',
            relatedTaskGroups: 'courseware-task-groups/related',
            userIsTeacher: 'userIsTeacher',
        }),
        elementUrls() {
            return this.peerReviews.reduce((memo, review) => {
                const task = this.tasks[review.id];
                const element = this.relatedStructuralElement({ parent: task, relationship: 'structural-element' });
                memo[review.id] = this.getLinkToElement(element);
                return memo;
            }, {});
        },
        peerReviews() {
            const course = { type: 'courses', id: this.context.id };
            return this.relatedPeerReviews({ parent: course, relationship: 'courseware-peer-reviews' }) ?? [];
        },
        processes() {
            return _.reverse(
                _.sortBy(
                    Object.values(
                        this.peerReviews.reduce((memo, review) => {
                            const process = this.relatedPeerReviewProcesses({
                                parent: review,
                                relationship: 'process',
                            });
                            memo[process.id] = process;
                            return memo;
                        }, {})
                    ),
                    ['attributes.chdate']
                )
            );
        },
        taskGroups() {
            return Object.values(this.processes).reduce((memo, process) => {
                memo[process.id] = this.relatedTaskGroups({ parent: process, relationship: 'task-group' });
                return memo;
            }, {});
        },
        tasks() {
            return this.peerReviews.reduce((memo, review) => {
                memo[review.id] = this.relatedTask({ parent: review, relationship: 'task' });
                return memo;
            }, {});
        },
    },
    methods: {
        ...mapActions({
            loadRelatedPeerReviews: 'courseware-peer-reviews/loadRelated',
        }),
        isPeerReviewProcessActive(process) {
            return getProcessStatus(process)?.status === ProcessStatus.Active;
        },
        isPeerReviewProcessAfter(process) {
            return getProcessStatus(process)?.status === ProcessStatus.After;
        },
        reviewsOf(process) {
            return this.peerReviews.filter((review) => review.relationships.process.data.id === process.id);
        },
        peerReviewsFromMe(process) {
            return this.reviewsOf(process).filter((process) => process.attributes['is-reviewer']);
        },
        peerReviewsForMe(process) {
            return this.reviewsOf(process).filter((process) => process.attributes['is-submitter']);
        },
    },
    mounted() {
        const parent = { type: 'courses', id: this.context.id };
        const relationship = 'courseware-peer-reviews';
        const options = {
            include: 'process,task.structural-element,task.task-group,reviewer,submitter',
        };
        this.loadRelatedPeerReviews({ parent, relationship, options }).then(() => (this.loading = false));
    },
};
</script>
