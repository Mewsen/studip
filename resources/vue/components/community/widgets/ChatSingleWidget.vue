<template>
    <WidgetWrapper :title="widgetTitle" v-bind="$attrs">
        <template #content>
            One single chat: {{ config.threadId }}
            <button @click="showChat">show the chat</button>
        </template>

        <template #settings>
            <h3 class="settings-header"></h3>
        </template>
    </WidgetWrapper>
</template>
<script setup>
import { computed } from 'vue';
import WidgetWrapper from '@/vue/components/widget/WidgetWrapper.vue';
import { useCommunityOverviewStore } from '@/vue/store/pinia/community/community-overview.js';

const props = defineProps({
    widgetData: {
        type: Object,
        required: true,
    },
});

const overviewStore = useCommunityOverviewStore();
const widgetTitle = 'ChatSingle';

const config = computed(() => {
    return props.widgetData.config;
});

function showChat() {
    overviewStore.openChatInDrawer(config.value.threadId);
}

</script>