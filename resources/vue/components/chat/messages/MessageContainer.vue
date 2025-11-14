<template>
    <div class="chat-container">
        <ol v-for="message in messages" :key="message.id" class="chat-message">
            <message :message="message" />
        </ol>
    </div>
</template>
<script setup>
import Message from '@/vue/components/chat/messages/Message.vue';

const props = defineProps({
    messages: {
        type: Array,
        required: true,
    },
});

</script>
<style lang="scss">
</style>