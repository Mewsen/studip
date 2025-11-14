<template>
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
import { useMessageStore } from '@/vue/store/pinia/chat/chat-messages.js';
import { useRoomStore } from '@/vue/store/pinia/chat/chat-rooms.js';

const props = defineProps({
    context: {
        type: String,
        required: true,
    },
});

const settingStore = useSettingStore();
const messageStore = useMessageStore();
const roomStore = useRoomStore();

const isCourseContext = computed(() => props.context === 'course');
const inCommunityContext = computed(() => props.context === 'community');

const sidebarEnabled = computed(() => isCourseContext.value || inCommunityContext.value);

const setupDummyData = () => {

    const dummyRooms = [
        { id: 'room-1', name: 'General Chat', lastMessage: '2024-06-01T10:00:00Z', 'unread-count': 2 },
        { id: 'room-2', name: 'Project Discussion', lastMessage: '2024-06-01T09:30:00Z', 'unread-count': 0 },
        { id: 'room-3', name: 'Random Talks', lastMessage: '2024-05-31T18:45:00Z', 'unread-count': 5 },
    ];

    dummyRooms.forEach(room => {
        roomStore.storeRecord(room); 
    });

    settingStore.setSelectedRoomById('room-1');

    const dummyMessages = [
        { 
            id: 'msg-101', 
            attributes: { 'content-html': 'Welcome to the General Chat!', 'created-at': '2024-06-01T09:50:00Z' }, 
            relationships: { 
                room: { data: { id: 'room-1', type: 'blubber-rooms' } },
                author: { data: { id: '205f3efb7997a0fc9755da2b535038da', type: 'users', 'formatted-name': 'Test Dozent', username: 'test_dozent'} }
            } 
        },
        { 
            id: 'msg-102', 
            attributes: { 'content-html': 'I have a quick question.', 'created-at': '2024-06-01T10:00:00Z' }, 
            relationships: { 
                room: { data: { id: 'room-1', type: 'blubber-rooms' } },
                author: { data: { id: 'e7a0a84b161f3e8c09b4a0a2e8a58147', type: 'users', 'formatted-name': 'Test Autor', username: 'test_autor'} }
            } 
        },
        { 
            id: 'msg-201', 
            attributes: { 'content-html': 'Initial planning document is ready.', 'created-at': '2024-06-01T09:20:00Z' }, 
            relationships: { 
                room: { data: { id: 'room-2', type: 'blubber-rooms' } },
                author: { data: { id: '205f3efb7997a0fc9755da2b535038da', type: 'users', 'formatted-name': 'Test Dozent', username: 'test_dozent' } }
            } 
        },
        { 
            id: 'msg-202', 
            attributes: { 'content-html': 'Looks good! Let\'s schedule a follow-up.', 'created-at': '2024-06-01T09:30:00Z' }, 
            relationships: { 
                room: { data: { id: 'room-2', type: 'blubber-rooms' } },
                author: { data: { id: '7e81ec247c151c02ffd479511e24cc03', type: 'users', 'formatted-name': 'Test Tutor', username: 'test_tutor' } }
            } 
        },
        { 
            id: 'msg-301', 
            attributes: { 'content-html': 'Anyone up for coffee later?', 'created-at': '2024-05-31T18:45:00Z' }, 
            relationships: { 
                room: { data: { id: 'room-3', type: 'blubber-rooms' } },
                author: { data: { id: '205f3efb7997a0fc9755da2b535038da', type: 'users', 'formatted-name': 'Test Dozent', username: 'test_dozent' } }
            } 
        },
    ];

    dummyMessages.forEach(message => {
        messageStore.storeRecord(message); 
    });
};

onBeforeMount(() => {
    console.log('TheChat mounted with context:', props.context);
    setupDummyData();
});
</script>
