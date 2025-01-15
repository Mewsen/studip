<template>
    <span class="peer-review-process-status" v-if="!filter || status.status === filter">
        <StudipIcon
            v-if="status.shape !== undefined"
            :shape="status.shape"
            :role="status.role"
            :title="status.description"
            aria-hidden="true"
        />
        <span :class="{'sr-only': !description }">{{ status.description }}</span>
    </span>
</template>
<script>
import StudipIcon from '../../../StudipIcon.vue';
import { getProcessStatus, ProcessStatus } from './definitions';

export default {
    components: { StudipIcon },
    props: {
        description: {
            type: Boolean,
            default: false,
        },
        filter: {
            type: String,
            default: null,
        },
        process: {
            type: Object,
            required: true,
        },
    },
    computed: {
        status() {
            return getProcessStatus(this.process);
        },
    },
};
</script>

<style scoped>
.peer-review-process-status {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}
</style>
