<template>
    <strong><p v-html="question.questiondata.description"></p></strong>
</template>

<script setup>
const props = defineProps({
    question: {
        type: Object,
        required: true
    }
})


</script>
