
<script setup>
import {computed} from 'vue';
import StudipIcon from '@/vue/components/StudipIcon.vue';
import {$gettext} from '@/assets/javascripts/lib/gettext';
import {useForumConfig} from '@/vue/store/pinia/forum/ForumConfig';

const forumConfig = useForumConfig();
const props = defineProps({
    topic_id: {
        type: String,
    }
});

const discussionCreateURL = computed(() => {
    return STUDIP.URLHelper.getURL(`dispatch.php/course/forum/discussions/edit?topic_id=${props.topic_id}`);
});

const addDiscussion = () => {
    STUDIP.Dialog.fromURL(
        discussionCreateURL.value,
        {
            width: '900',
            height: '750'
        }
    );
}
</script>

<template>
    <button
        type="button"
        class="button button--icon-only"
        v-if="!forumConfig.allowGuestAccess"
        @click="addDiscussion"
        :title="$gettext('Neue Diskussion starten')"
        :aria-label="$gettext('Neue Diskussion starten')"
    >
        <StudipIcon shape="add" :size="20" aria-hidden="true" />
    </button>
</template>
