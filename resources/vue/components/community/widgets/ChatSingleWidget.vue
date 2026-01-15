<template>
    <WidgetWrapper :title="widgetTitle" :widget-data="props.widgetData" v-bind="$attrs" @update-config="handleConfigUpdate">
        <template #content>
            <p>Chat with Name</p>
            <div class="blubber_thread">
                <div class="scrollable_area">
                    <ol class="comments">
                        <li class="talk-bubble-wrapper">
                            <div class="talk-bubble-avatar"></div>
                            <div class="talk-bubble"></div>
                        </li>
                    </ol>
                </div>
            </div>
            <button @click="showChat">show the chat</button>
        </template>

        <template #settings>
            <form class="default">
                <label>
                    {{ $gettext('Chat-Raum') }}
                    <select v-model="formPayload['thread-id']">
                        <option value="1">Testraum 1</option>
                        <option value="2">Testraum 2</option>
                        <option value="3">Testraum 3</option>
                    </select>
                </label>
            </form>
        </template>
    </WidgetWrapper>
</template>
<script setup>
import { computed, ref } from 'vue';
import WidgetWrapper from '@/vue/components/widget/WidgetWrapper.vue';
import { useCommunityOverviewStore } from '@/vue/store/pinia/community/community-overview.js';
import { useWidgetStore } from '@/vue/store/pinia/widget/dashboard-widgets.js';

const props = defineProps({
    widgetData: {
        type: Object,
        required: true,
    },
});

const overviewStore = useCommunityOverviewStore();
const widgetStore = useWidgetStore();
const widgetTitle = 'ChatSingle';

const formPayload = ref(JSON.parse(JSON.stringify(props.widgetData.payload)));

const threadId = computed(() => {
    return props.widgetData.payload['thread-id'];
});

function showChat() {
    overviewStore.openChatInDrawer(threadId.value);
}

function handleConfigUpdate() {
    widgetStore.updateWidgetPayload(props.widgetData.id, formPayload.value);
}
</script>
<style lang="scss" scoped>
    .blubber_thread {
        margin-bottom: 10px;
    }
</style>
