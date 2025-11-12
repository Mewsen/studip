<template>
    <h2>The Chat</h2>
    <template v-if="sidebarEnabled">
        <Teleport to="#chat-rooms-widget" name="sidebar-chat-rooms">
            <chat-room-list />
        </Teleport>
        <Teleport to="#chat-room-actions-widget" name="sidebar-chat-room-actions">
            <chat-room-actions />
        </Teleport>
    </template>
    <the-chat-room />
    <detail-drawer />
</template>
<script setup>
import { computed, onBeforeMount } from 'vue';
import ChatRoomList from '@/vue/components/chat/sidebar/ChatRoomList.vue';
import ChatRoomActions from '@/vue/components/chat/sidebar/ChatRoomActions.vue';
import TheChatRoom from '@/vue/components/chat/TheChatRoom.vue';
import DetailDrawer from '@/vue/components/chat/details/DetailDrawer.vue';

import { useSettingStore } from '@/vue/store/pinia/chat/chat-settings.js';

const props = defineProps({
    context: {
        type: String,
        required: true,
    },
});

const settingStore = useSettingStore();

const isCourseContext = computed(() => props.context === 'course');
const inCommunityContext = computed(() => props.context === 'community');

const sidebarEnabled = computed(() => isCourseContext.value || inCommunityContext.value);

onBeforeMount(() => {
    console.log('TheChat mounted with context:', props.context);
    console.log('Something from settingStore:', settingStore.testing);
});
</script>
