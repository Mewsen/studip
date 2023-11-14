<template>
    <div class="cw-peer-review-processes-wrapper" v-if="!userIsTeacher">
        <StudipArticle>
            <template #title>
                {{ $gettext('Peer-Reviews von Ihnen') }}
            </template>
            <template #body>
                <table class="default" v-if="peerReviewsGiven.length">
                    <thead>
                        <tr>
                            <th>{{ $gettext('Status') }}</th>
                            <th>{{ $gettext('Bearbeitungszeit') }}</th>
                            <th>{{ $gettext('Aufgabe') }}</th>
                            <th>{{ $gettext('Aufgabe bearbeitet von') }}</th>
                            <th class="actions">
                                <span class="sr-only">{{ $gettext('Aktionen') }}</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="review in peerReviewsGiven" :key="review.id">
                            <td>
                                <ProcessStatus :process="processes[review.id]" />
                            </td>
                            <td>
                                <StudipDate :date="new Date(processes[review.id].attributes['review-start'])" />
                                -
                                <StudipDate :date="new Date(processes[review.id].attributes['review-end'])" />
                            </td>
                            <td>
                                <a :href="elementUrls[review.id]">
                                    {{ taskGroups[review.id].attributes.title }}
                                </a>
                            </td>
                            <td>
                                {{ submitterOf(review)?.attributes['formatted-name'] ?? $gettext('anonym') }}
                            </td>
                            <td class="actions">
                                <template v-if="review.attributes.assessment">
                                    <button class="button" @click="onShowPeerReview(review)">
                                        {{ $gettext('Peer-Review anzeigen') }}
                                    </button>
                                </template>
                                <template v-else>
                                    {{ $gettext('Kein Peer-Review abgegeben') }}
                                </template>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <CompanionBox
                    v-else-if="!loading"
                    mood="sad"
                    :msgCompanion="$gettext('Sie haben noch keine Peer-Reviews gegeben.')"
                />
            </template>
        </StudipArticle>
        <StudipArticle>
            <template #title>
                {{ $gettext('Peer-Reviews für Sie') }}
            </template>
            <template #body>
                <table class="default" v-if="peerReviewsReceived.length">
                    <thead>
                        <tr>
                            <th>{{ $gettext('Status') }}</th>
                            <th>{{ $gettext('Bearbeitungszeit') }}</th>
                            <th>{{ $gettext('Aufgabe') }}</th>
                            <th>{{ $gettext('Peer-Review von') }}</th>
                            <th class="actions">
                                <span class="sr-only">{{ $gettext('Aktionen') }}</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="review in peerReviewsReceived" :key="review.id">
                            <td>
                                <ProcessStatus :process="processes[review.id]" />
                            </td>
                            <td>
                                <StudipDate :date="new Date(processes[review.id].attributes['review-start'])" />
                                -
                                <StudipDate :date="new Date(processes[review.id].attributes['review-end'])" />
                            </td>
                            <td>
                                <a :href="elementUrls[review.id]">
                                    {{ taskGroups[review.id].attributes.title }}
                                </a>
                            </td>
                            <td>
                                {{ reviewerOf(review)?.attributes['formatted-name'] ?? $gettext('anonym') }}
                            </td>
                            <td>
                                <template v-if="review.attributes.assessment">
                                    <button class="button" @click="onShowPeerReview(review)">
                                        {{ $gettext('Peer-Review anzeigen') }}
                                    </button>
                                </template>
                                <template v-else>
                                    {{ $gettext('Kein Peer-Review abgegeben') }}
                                </template>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <CompanionBox
                    v-else-if="!loading"
                    mood="sad"
                    :msgCompanion="$gettext('Sie haben noch keine Peer-Reviews erhalten.')"
                />
            </template>
        </StudipArticle>
        <ResultDialog v-model="showPeerReview" v-if="selectedPeerReview" :review="selectedPeerReview" />
    </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex';
import CompanionBox from '../../layouts/CoursewareCompanionBox.vue';
import ProcessStatus from './ProcessStatus.vue';
import ResultDialog from './ResultDialog.vue';
import StudipArticle from '../../../StudipArticle.vue';
import StudipDate from '../../../StudipDate.vue';
import UserAvatar from '@/vue/components/StudipUserAvatar.vue';
import taskHelper from '../../../../mixins/courseware/task-helper.js';

export default {
    components: {
        CompanionBox,
        ProcessStatus,
        ResultDialog,
        StudipArticle,
        StudipDate,
    },
    props: {},
    mixins: [taskHelper],
    data: () => ({
        loading: true,
        showPeerReview: false,
        selectedPeerReview: null,
    }),
    computed: {
        ...mapGetters({
            context: 'context',
            currentUser: 'currentUser',
            relatedPeerReviewProcesses: 'courseware-peer-review-processes/related',
            relatedPeerReviews: 'courseware-peer-reviews/related',
            relatedStructuralElement: 'courseware-structural-elements/related',
            relatedTask: 'courseware-tasks/related',
            relatedTaskGroups: 'courseware-task-groups/related',
            relatedUsers: 'users/related',
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
        peerReviewsGiven() {
            return this.peerReviews.filter((peerReview) => peerReview.relationships.reviewer.data?.id === this.userId);
        },
        peerReviewsReceived() {
            return this.peerReviews.filter((peerReview) => peerReview.relationships.submitter.data?.id === this.userId);
        },
        processes() {
            return this.peerReviews.reduce((memo, review) => {
                memo[review.id] = this.relatedPeerReviewProcesses({ parent: review, relationship: 'process' });
                return memo;
            }, {});
        },
        taskGroups() {
            return this.peerReviews.reduce((memo, review) => {
                const process = this.processes[review.id];
                memo[review.id] = this.relatedTaskGroups({ parent: process, relationship: 'task-group' });
                return memo;
            }, {});
        },
        tasks() {
            return this.peerReviews.reduce((memo, review) => {
                memo[review.id] = this.relatedTask({ parent: review, relationship: 'task' });
                return memo;
            }, {});
        },
        userId() {
            return this.currentUser.id;
        },
    },
    methods: {
        ...mapActions({
            loadRelatedPeerReviews: 'courseware-peer-reviews/loadRelated',
        }),
        onShowPeerReview(review) {
            this.selectedPeerReview = review;
            this.showPeerReview = true;
        },
        reviewerOf(review) {
            return this.relatedUsers({ parent: review, relationship: 'reviewer' });
        },
        submitterOf(review) {
            return this.relatedUsers({ parent: review, relationship: 'submitter' });
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

<style>
.cw-peer-review-processes-wrapper > * + * {
    margin-block-start: 3rem;
}
</style>
