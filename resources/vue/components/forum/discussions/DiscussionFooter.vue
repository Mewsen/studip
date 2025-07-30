<script setup>
import StudipDateTime from "../../StudipDateTime.vue";
import StudipIcon from "../../StudipIcon.vue";
import ForumMembers from "../ForumMembers.vue";
import {$gettext} from "../../../../assets/javascripts/lib/gettext";
import {numberFormatter} from "../../../../assets/javascripts/lib/number_formatter";
import {useForumConfig} from "../../../store/pinia/forum/ForumConfig";

const postCreateForm = defineModel('postCreateForm');

const forumConfig = useForumConfig();
defineProps({
    discussion: {
        type: Object,
        required: true
    },
    posts_count: {
        type: Number,
        default: 0
    },
    recent_activity: {
        type: String,
    }
});
</script>

<template>
    <div class="discussion__status">
        <div class="flex items-start gap-20">
            <div class="text-center">
                <p>{{ $gettext('Erstellt') }}</p>
                <StudipDateTime :iso="discussion.mkdate" :date_only="true" />
            </div>
            <div class="text-center">
                <p>{{ $gettext('Beiträge') }}</p>
                <p>{{ posts_count }}</p>
            </div>
            <div class="text-center">
                <p>{{ $gettext('Aufrufe') }}</p>
                <p>{{ numberFormatter(discussion.view_count, 1) }}</p>
            </div>
            <div class="text-center">
                <p>{{ $gettext('Aktivität') }}</p>
                <StudipDateTime v-if="recent_activity" :iso="recent_activity" :relative="true" />
                <StudipDateTime v-else :iso="discussion.mkdate" :relative="true"/>
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
</template>
