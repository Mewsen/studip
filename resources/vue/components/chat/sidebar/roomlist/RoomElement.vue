<template>
    <div class="room-element">
        <button type="button" class="room-main-button" @click="navigateToRoom" title="Zum Chat-Raum wechseln">
            <img :src="roomAvatar" alt="Room Avatar" class="room-avatar" />
            <div class="room-info">
                <span class="room-name">{{ roomName }}</span>
                <div class="room-last-message">{{ roomLastMessageDate }}</div>
            </div>
        </button>
        <div class="room-actions" :class="{ 'has-unread': unreadCount > 0 }">
            <div class="room-unread-indicator"></div>
            <studip-context-menu :title="$gettext('Raum Optionen')" button-shape="settings" button-class="borderless">
                <template #content> testing... </template>
            </studip-context-menu>
        </div>
    </div>
</template>
<script setup>
import { computed } from 'vue';
import StudipContextMenu from '@/vue/components/StudipContextMenu.vue';
import { useSettingStore } from '@/vue/store/pinia/chat/chat-settings.js';
const props = defineProps({
    room: {
        type: Object,
        required: true,
    },
});

const settingStore = useSettingStore();

const roomAvatar = computed(() => {
    return props.room?.avatarUrl || STUDIP.URLHelper.base_url + 'assets/images/avatars/course/nobody_small.webp';
});

const roomName = computed(() => {
    return props.room?.name || '---';
});

const roomLastMessageDate = computed(() => {
    return props.room?.lastMessage || '';
});

const roomId = computed(() => {
    return props.room?.id;
});

const unreadCount = computed(() => {
    return props.room?.['unread-count'] || 0;
});

const navigateToRoom = () => {
    settingStore.setSelectedRoomId(roomId.value);
    document.getElementById('chat-input').focus();
};
</script>
<style lang="scss">
.room-element {
    display: flex;
    align-items: center;
    padding: 10px 15px;
    margin-bottom: 8px;
    background-color: #ffffff;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    transition: background-color 0.2s ease;

    &:hover {
        background-color: #f5f5f5;
        cursor: pointer;
    }

    .room-main-button {
        flex-grow: 1;
        display: flex;
        align-items: center;
        background: none;
        border: none;
        padding: 0;
        margin: 0;
        text-align: left;
        cursor: pointer;

        &:hover {
            cursor: pointer;
        }

        .room-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 15px;
            border: 2px solid #e0e0e0;
        }

        .room-info {
            flex-grow: 1;
            min-width: 0;

            .room-name {
                display: block;
                font-weight: bold;
                color: #333333;
                text-decoration: none;
                margin-bottom: 2px;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            .room-last-message {
                font-size: 0.85em;
                color: #777777;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }
        }
    }

    .room-actions {
        flex-shrink: 0;
        position: relative;
        width: 30px;
        height: 30px;
        z-index: 5;

        .context-menu {
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.2s ease;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        .room-unread-indicator {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 10px;
            height: 10px;
            background-color: var(--color--warning);
            border-radius: 50%;
            opacity: 0;
            transition: opacity 0.2s ease;
        }
    }

    .room-actions.has-unread {
        .room-unread-indicator {
            opacity: 1;
        }
    }

    .room-main-button:hover + .room-actions,
    .room-main-button:focus + .room-actions,
    .room-actions:hover,
    .room-actions:focus-within {
        .context-menu {
            opacity: 1;
            pointer-events: auto;
        }
        .room-unread-indicator {
            opacity: 0;
        }
    }

    .borderless.button {
        border: none;
    }
}
</style>
