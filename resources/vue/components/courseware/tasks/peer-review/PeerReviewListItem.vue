<template>
    <tr>
        <td>
            <a :href="getLinkToElement(element)">
                {{ taskGroup.attributes.title }}
            </a>
        </td>
        <td>
            <a v-if="isUser(submitter)" :href="userProfile(submitter)">
                <UserAvatar
                    :avatar-url="submitter.meta.avatar.small"
                    :formatted-name="submitter.attributes['formatted-name']"
                    small
                />
            </a>
            <a v-else :href="statusGroupUrl(submitter)">
                {{ submitter.attributes.name }}
            </a>
        </td>
        <td>
            <a v-if="isUser(reviewer)" :href="userProfile(reviewer)">
                <UserAvatar
                    :avatar-url="reviewer.meta.avatar.small"
                    :formatted-name="reviewer.attributes['formatted-name']"
                    small
                />
            </a>
            <a v-else :href="statusGroupUrl(reviewer)">
                {{ reviewer.attributes.name }}
            </a>
        </td>
        <td>
            <button class="button" @click="onShowAssessment" :disabled="canShowReview">{{ $gettext('Peer-Review anzeigen') }}</button>
        </td>
    </tr>
</template>

<script>
import { mapGetters } from 'vuex';
import UserAvatar from '@/vue/components/StudipUserAvatar.vue';
import taskHelper from '../../../../mixins/courseware/task-helper.js';
import { getProcessStatus, ProcessStatus } from './definitions';

export default {
    mixins: [taskHelper],
    props: {
        process: {
            type: Object,
            required: true,
        },
        review: {
            type: Object,
            required: true,
        },
        taskGroup: {
            type: Object,
            required: true,
        },
    },
    components: { UserAvatar },
    computed: {
        ...mapGetters({
            context: 'context',
            relatedStructuralElement: 'courseware-structural-elements/related',
            relatedTasks: 'courseware-tasks/related',
            relatedStatusGroups: 'status-groups/related',
            relatedUsers: 'users/related',
        }),
        canShowReview() {
            return getProcessStatus(this.process).status !== ProcessStatus.After;
        },
        element() {
            const parent = { id: this.task.id, type: this.task.type };
            const relationship = 'structural-element';
            return this.relatedStructuralElement({ parent, relationship });
        },
        reviewer() {
            const user = this.relatedUsers({ parent: this.review, relationship: 'reviewer' });
            if (user) {
                return user;
            }
            const statusGroup = this.relatedStatusGroups({ parent: this.review, relationship: 'reviewer' });
            return statusGroup;
        },
        submitter() {
            const user = this.relatedUsers({ parent: this.task, relationship: 'solver' });
            if (user) {
                return user;
            }
            const statusGroup = this.relatedStatusGroups({ parent: this.task, relationship: 'solver' });
            return statusGroup;
        },
        task() {
            const parent = { id: this.review.id, type: this.review.type };
            const relationship = 'task';
            return this.relatedTasks({ parent, relationship });
        },
    },
    methods: {
        isUser(object) {
            return object.type === 'users';
        },
        onShowAssessment() {
            console.debug('NYI');
        },
        statusGroupUrl(statusGroup) {
            const cid = this.context.id;
            return window.STUDIP.URLHelper.getURL(
                'dispatch.php/course/statusgroups',
                { cid, contentbox_open: statusGroup.id },
                true
            );
        },
        userProfile(user) {
            const username = user.attributes.username;
            return window.STUDIP.URLHelper.getURL('dispatch.php/profile', { username }, true);
        },
    },
};
</script>
