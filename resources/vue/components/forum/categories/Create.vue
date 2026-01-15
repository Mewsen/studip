
<script setup>
import StudipIcon from '@/vue/components/StudipIcon.vue';
import {getCategoryCreateURL} from '@/vue/components/forum/helpers/urls';
import {$gettext} from '@/assets/javascripts/lib/gettext';
import {useForumConfig} from '@/vue/store/pinia/forum/ForumConfig';

const forumConfig = useForumConfig();

defineProps({
    label: {
        type: String,
        default: ''
    }
});

const addCategory = () => {
    STUDIP.Dialog.fromURL(
        getCategoryCreateURL(),
        {
            width: '700'
        }
    );
}
</script>

<template>
    <button
        v-if="forumConfig.isModerator"
        type="button"
        class="button"
        @click="addCategory"
        :title="$gettext('Neue Kategorie anlegen')"
        :class="label ? 'button--icon-label' : 'button--icon-only'"
    >
        <StudipIcon shape="add" :size="20" aria-hidden="true" />
        <span v-if="label" class="label">{{ label }}</span>
    </button>
</template>
