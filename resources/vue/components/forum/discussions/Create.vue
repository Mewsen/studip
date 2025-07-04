
<script setup>
import {useForumConfig} from "../../../store/pinia/forum/ForumConfig";
import StudipIcon from "@/vue/components/StudipIcon.vue";
import {computed} from "vue";
import {$gettext} from "@/assets/javascripts/lib/gettext";

const forumConfig = useForumConfig();
const props = defineProps({
    topic_id: {
        type: String,
    }
});

const discussionCreateURL = computed(() => {
    return STUDIP.URLHelper.getURL(`dispatch.php/course/forum/discussions/edit?topic_id=${props.topic_id}`);
});
</script>

<template>
    <a
        v-if="forumConfig.isModerator"
        :href="discussionCreateURL"
        :title="$gettext('Neue Diskussion starten')"
        data-dialog="width=900;height=750"
        type="button"
        class="icon-button">
        <StudipIcon shape="add" :size="20" aria-hidden="true" />
    </a>
</template>
