<template>
    <div class="room-element">
        <img :src="roomAvatar" alt="Room Avatar" class="room-avatar" />
        <div class="room-info">
            <a :href="`/chat/rooms/${roomId}`" class="room-name">{{ roomName }}</a>
            <div class="room-last-message">{{ roomLastMessageDate }}</div>
        </div>
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
const props = defineProps({
    room: {
        type: Object,
        required: true,
    },
});

const roomAvatar = computed(() => {
    return props.room.avatarUrl || STUDIP.URLHelper.base_url + 'assets/images/avatars/course/nobody_small.webp';
});

const roomName = computed(() => {
    return props.room.name || '---';
});

const roomLastMessageDate = computed(() => {
    return props.room.lastMessage || '';
});

const roomId = computed(() => {
    return props.room.id;
});

const unreadCount = computed(() => {
    return props.room['unread-count'] || 0;
});
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

    .room-actions {
        margin-left: 15px;
        flex-shrink: 0;
        position: relative;
        width: 30px;
        height: 30px;

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
            z-index: 10;
        }
    }

    .room-actions.has-unread {
        .room-unread-indicator {
            opacity: 1;
        }
    }

    &:hover,
    &:focus-within {
        .room-actions {
            .context-menu {
                opacity: 1;
                pointer-events: auto;
            }
            .room-unread-indicator {
                opacity: 0;
            }
        }
    }

    .borderless.button {
        border: none;
    }
}
</style>
