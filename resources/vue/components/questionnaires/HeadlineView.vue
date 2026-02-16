<template>
    <strong><p v-html="question.questiondata.description"></p></strong>
</template>

<script>
export default {
    name: 'HeadlineView',
    props: {
        question: Array
    },
};


</script>
