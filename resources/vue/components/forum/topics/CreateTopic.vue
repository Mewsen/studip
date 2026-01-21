
<script setup>
import {computed} from 'vue';
import StudipIcon from '@/vue/components/StudipIcon.vue';
import {useForumConfig} from '@/vue/store/pinia/forum/ForumConfig';

const forumConfig = useForumConfig();

const props = defineProps({
    category_id: {
        type: String,
    },
    label: {
        type: String,
        default: ''
    }
});

const topicCreateURL = computed(() => {
    if (props.category_id) {
        return STUDIP.URLHelper.getURL(`dispatch.php/course/forum/topics/edit?category_id=${props.category_id}`);
    }

    return STUDIP.URLHelper.getURL('dispatch.php/course/forum/topics/edit');
});

const addTopic = () => {
    STUDIP.Dialog.fromURL(
        topicCreateURL.value,
        {
            width: '700'
        }
    );
}
</script>

<template>
    <button
        type="button"
        class="button button--icon-only"
        v-if="forumConfig.isModerator"
        @click="addTopic"
        :title="$gettext('Neues Thema anlegen')"
        :aria-label="$gettext('Neues Thema anlegen')"
        :class="label ? 'button--icon-label' : 'button--icon-only'"
    >
        <StudipIcon shape="add" :size="20" aria-hidden="true" />
        <span v-if="label" class="label">{{ label }}</span>
    </button>
</template>
