<template>
    <div v-if="peerReviews && peerReviews.length > 0">
        <table class="default">
            <thead>
                <tr>
                    <th>{{ $gettext("Aufgabe") }}</th>
                    <th>{{ $gettext("Lösung von") }}</th>
                    <th>{{ $gettext("Peer-Review von") }}</th>
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
                    />
            </tbody>
        </table>
    </div>
</template>

<script>
import { mapGetters } from 'vuex';
import PeerReviewListItem from './PeerReviewListItem.vue';

export default {
    components: { PeerReviewListItem },
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
    computed: {
        ...mapGetters({
            relatedPeerReviews: 'courseware-peer-reviews/related',
        }),
        peerReviews() {
            return this.relatedPeerReviews({ parent: this.process, relationship: 'peer-reviews' });
        },
    },
};
</script>
