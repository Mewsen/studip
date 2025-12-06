<template>
    <h3>Chat</h3>
    <p>thread id : {{ threadId }}</p>
</template>
<script setup>
const props = defineProps({
    threadId: {
        type: String,
        required: true,
    },
});
</script>
