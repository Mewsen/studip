
<script setup>
import StudipIcon from "@/vue/components/StudipIcon.vue";
import {getCategoryCreateURL} from "../helpers/urls";
import {$gettext} from "@/assets/javascripts/lib/gettext";

defineProps({
    label: {
        type: String,
        default: ''
    }
});
</script>

<template>
    <a
        :href="getCategoryCreateURL()"
        data-dialog="size=700"
        :title="$gettext('Neue Kategorie anlegen')"
        :aria-label="$gettext('Neue Kategorie anlegen')"
        class="button"
        :class="label ? 'button--icon-label' : 'button--icon-only'"
        role="button"
    >
        <StudipIcon shape="add" :size="20" aria-hidden="true" />
        <span v-if="label" class="label">{{ label }}</span>
    </a>
</template>
