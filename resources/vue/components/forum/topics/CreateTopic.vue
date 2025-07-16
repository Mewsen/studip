
<script setup>
import StudipIcon from "@/vue/components/StudipIcon.vue";
import {computed} from "vue";

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
</script>

<template>
    <a
        :href="topicCreateURL"
        data-dialog="width=700"
        :title="$gettext('Neues Thema anlegen')"
        :aria-label="$gettext('Neues Thema anlegen')"
        class="button button--icon-only"
        :class="label ? 'button--icon-label' : 'button--icon-only'"
        role="button"
    >
        <StudipIcon shape="add" :size="20" aria-hidden="true" />
        <span v-if="label" class="label">{{ label }}</span>
    </a>
</template>
