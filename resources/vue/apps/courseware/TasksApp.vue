<template>
    <RouterView />
</template>

<script>
import { RouterView } from 'vue-router';

export default {
    components: { RouterView },
    async beforeCreate() {
        STUDIP.loadChunk('courseware');

        const { id } = this.$store.getters['context'];

        this.$store.dispatch('setUserId', STUDIP.USER_ID);
        await this.$store.dispatch('users/loadById', { id: STUDIP.USER_ID });
        await this.$store.dispatch('tasks/loadTasksOfCourse', { cid: id });
    },
};
</script>
