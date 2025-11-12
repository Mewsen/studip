<template>
    <section class="studip-chat">
        <header class="chat-header">
            <h2>Chat Room</h2>
        </header>
        <message-container />
        <div class="chat-input-area">
            <input type="text" placeholder="Type your message..." />
            <button>Send</button>
        </div>
    </section>

</template>

<script setup>
import MessageContainer from '@/vue/components/chat/messages/MessageContainer.vue';
</script>
