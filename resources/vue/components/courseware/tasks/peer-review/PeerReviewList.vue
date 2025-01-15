<template>
    <div v-if="peerReviews && peerReviews.length > 0">
        <table class="default">
            <thead>
                <tr>
                    <th>{{ $gettext('Aufgabe') }}</th>
                    <th>{{ $gettext('Lösung von') }}</th>
                    <th>{{ $gettext('Peer-Review von') }}</th>
                    <th>&nbsp;</th>
                </tr>
            </thead>
            <tbody>
                <PeerReviewListItem
                    v-for="review in peerReviews"
                    :review="review"
                    :key="review.id"
                    :process="process"
                    :task-group="taskGroup"
                    @show-assessment="onShowAssessment(review)"
                />
            </tbody>
        </table>
        <PeerReviewResultDialog v-model:show="showPeerReview" v-if="selectedPeerReview" :review="selectedPeerReview" />
    </div>
    <div v-else>
        {{ $gettext("Bisher sind noch keine Peer-Review-Paarungen erstellt worden.") }}
    </div>
</template>

<script>
import { mapGetters } from 'vuex';
import PeerReviewListItem from './PeerReviewListItem.vue';
import PeerReviewResultDialog from './ResultDialog.vue';

export default {
    components: { PeerReviewListItem, PeerReviewResultDialog },
    props: {
        process: {
            type: Object,
            required: true,
        },
        taskGroup: {
            type: Object,
            required: true,
        },
    },
    data: () => ({
        selectedPeerReview: null,
        showPeerReview: false,
    }),
    computed: {
        ...mapGetters({
            relatedPeerReviews: 'courseware-peer-reviews/related',
        }),
        peerReviews() {
            return this.relatedPeerReviews({ parent: this.process, relationship: 'peer-reviews' });
        },
    },
    methods: {
        onShowAssessment(review) {
            this.selectedPeerReview = review;
            this.showPeerReview = true;
        },
    },
};
</script>
