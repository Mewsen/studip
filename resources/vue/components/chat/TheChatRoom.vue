<template>
    <section class="studip-chat" :class="{ 'drawer-open': drawerOpen }">
        <chat-room-header :room="selectedRoom" />
        <message-container :messages="roomMessages" />
        <div class="chat-input-area">
            <textarea v-model="newMessage" id="chat-input" :placeholder="$gettext('Nachricht senden …')" :style="textareaStyle" />
            <button class="button icon-button" :title="$gettext('Anhang')">
                <studip-icon shape="staple" :size="22" />
            </button>
            <button class="button" :title="$gettext('Nachricht senden')">
                <studip-icon shape="blubber" :size="22" />
            </button>
        </div>
    </section>
</template>

<script setup>
import { computed, ref } from 'vue';
import { useSettingStore } from '@/vue/store/pinia/chat/chat-settings.js';
import { useMessageStore } from '@/vue/store/pinia/chat/chat-messages.js';
import MessageContainer from '@/vue/components/chat/messages/MessageContainer.vue';
import ChatRoomHeader from '@/vue/components/chat/ChatRoomHeader.vue';

const settingStore = useSettingStore();
const messageStore = useMessageStore();

const newMessage = ref('');

const selectedRoom = computed(() => settingStore.selectedRoom);
const drawerOpen = computed(() => settingStore.showDetailsDrawer);
const roomMessages = computed(() => {
    if (selectedRoom) {
        return messageStore.messagesInSelectedRoom;
    }
    return [];
});

const lineCount = computed(() => {
    return (newMessage.value.match(/\n/g) || []).length + 1;
});

const textareaStyle = computed(() => {
    const lineHeight = 20;
    let height = lineHeight + 'px';

    if (lineCount.value === 2) {
        height = lineHeight * 2 + 'px';
    } else if (lineCount.value >= 3) {
        height = lineHeight * 3 + 'px';
    }

    return {
        height: height,
    };
});
</script>
<style lang="scss">
.studip-chat {
    display: flex;
    flex-direction: column;
    height: 100%;
    gap: 16px;

    &.drawer-open {
        width: calc(100% - 320px);
    }

    .chat-header {
    }

    .chat-container,
    .chat-input-area {
        margin: 0 auto;
        max-width: 1200px;
        width: 100%;
    }

    .chat-container {
        flex-grow: 1;
        overflow: scroll;
    }

    .chat-input-area {
        display: flex;
        padding: 10px;
        gap: 10px;
        border-top: 1px solid var(--color--divider);

        textarea {
            flex-grow: 1;
            resize: none;
            padding: 10px;
            border-radius: var(--border-radius-default);
            border: none;

            &:focus-visible {
                outline: none;
            }
        }

        button.button {
            margin: 0;
            min-width: unset;
            padding: 7px;
            width: 40px;
            height: 40px;

            .studip-icon {
                vertical-align: middle;
            }
        }
    }
}
</style>
