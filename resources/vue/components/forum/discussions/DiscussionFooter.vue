<script setup>
import StudipDateTime from "../../StudipDateTime.vue";
import StudipIcon from "../../StudipIcon.vue";
import ForumMembers from "../ForumMembers.vue";
import {$gettext} from "../../../../assets/javascripts/lib/gettext";
import {numberFormatter} from "../../../../assets/javascripts/lib/number_formatter";
import {useForumConfig} from "../../../store/pinia/forum/ForumConfig";
import {computed} from "vue";

const postCreateForm = defineModel('postCreateForm');

const forumConfig = useForumConfig();
const props = defineProps({
    discussion: {
        type: Object,
        required: true
    },
    posts: {
        type: Array,
        required: true
    },
    read_index: {
        type: Number,
        default: -1
    }
});

const recentActivity = computed(() => props.posts.at(-1)?.mkdate ?? props.discussion.mkdate);
const hasUnreadPost = computed(() => {
    return props.read_index === 0 && props.posts.length > 1 && props.posts[1].author.id !== STUDIP.USER_ID;
});
</script>

<template>
    <div class="discussion__footer">
        <div v-if="!forumConfig.allowGuestAccess && hasUnreadPost" class="post__unread">
        </div>
        <div class="discussion__status">
            <div class="flex items-start gap-20">
                <div class="text-center">
                    <p>{{ $gettext('Erstellt') }}</p>
                    <StudipDateTime :iso="discussion.mkdate" :date_only="true" />
                </div>
                <div class="text-center">
                    <p>{{ $gettext('Beiträge') }}</p>
                    <p>{{ posts.length }}</p>
                </div>
                <div class="text-center">
                    <p>{{ $gettext('Aufrufe') }}</p>
                    <p>{{ numberFormatter(discussion.view_count, 1) }}</p>
                </div>
                <div class="text-center">
                    <p>{{ $gettext('Aktivität') }}</p>
                    <StudipDateTime :iso="recentActivity" :relative="true" />
                </div>
            </div>
            <ForumMembers :members="discussion.members" :limit="5" size="35px" />
            <a
                v-if="!forumConfig.allowGuestAccess && !discussion.closed_at"
                href="#new-post"
                class="button button--icon-label"
                role="button"
                :title="$gettext('Antworten')"
                :aria-label="$gettext('Antworten')"
                :class="{
                    'disabled': postCreateForm
                }"
                @click="postCreateForm = true"
            >
                <StudipIcon shape="reply" :size="20" aria-hidden="true" />
                {{ $gettext('Antworten') }}
            </a>
        </div>
    </div>
</template>
