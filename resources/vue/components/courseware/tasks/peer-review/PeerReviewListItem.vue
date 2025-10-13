<template>
    <tr>
        <td>
            <a :href="getLinkToElement(element)">
                {{ taskGroup.attributes.title }}
            </a>
        </td>
        <td>
            <UserAvatarDropdown
                v-if="isUser(submitter)"
                :user="{
                    id: submitter.id,
                    avatar_url: submitter.meta.avatar.small,
                    username: submitter.attributes['username'],
                    name: submitter.attributes['formatted-name']
                }"
                :withName="true"
            />
            <a v-else :href="statusGroupUrl(submitter)">
                {{ submitter.attributes.name }}
            </a>
        </td>
        <td>
            <UserAvatarDropdown
                v-if="isUser(reviewer)"
                :user="{
                    id: reviewer.id,
                    avatar_url: reviewer.meta.avatar.small,
                    username: reviewer.attributes['username'],
                    name: reviewer.attributes['formatted-name']
                }"
                :withName="true"
            />
            <a v-else :href="statusGroupUrl(reviewer)">
                {{ reviewer.attributes.name }}
            </a>
        </td>
        <td>
            <template v-if="isPeerReviewAfter">
                <template v-if="review.attributes.assessment">
                    <button class="button" @click="onShowAssessment(review)">
                        {{ $gettext('Peer-Review anzeigen') }}
                    </button>
                </template>
                <template v-else>
                    {{ $gettext('Kein Peer-Review abgegeben') }}
                </template>
            </template>
            <template v-else>
                {{ $gettext('Peer-Review sichtbar ab:') }}
                <StudipDate :date="new Date(process.attributes['review-end'])" />
            </template>
        </td>
    </tr>
</template>

<script>
import { mapGetters } from 'vuex';
import StudipDate from '@/vue/components/StudipDate.vue';
import taskHelper from '../../../../mixins/courseware/task-helper.js';
import { getProcessStatus, ProcessStatus } from './definitions';
import UserAvatarDropdown from "@/vue/components/avatar/UserAvatarDropdown.vue";

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
    components: { StudipDate, UserAvatarDropdown },
    computed: {
        ...mapGetters({
            context: 'context',
            relatedStructuralElement: 'courseware-structural-elements/related',
            relatedTasks: 'courseware-tasks/related',
            relatedStatusGroups: 'status-groups/related',
            relatedUsers: 'users/related',
        }),
        element() {
            const parent = { id: this.task.id, type: this.task.type };
            const relationship = 'structural-element';
            return this.relatedStructuralElement({ parent, relationship });
        },
        isPeerReviewAfter() {
            return getProcessStatus(this.process)?.status === ProcessStatus.After;
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
            this.$emit('show-assessment');
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
