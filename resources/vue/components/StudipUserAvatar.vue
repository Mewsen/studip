<template>
    <div class="studip-user-avatar" :class="{ 'studip-user-avatar-small': small }">
        <span>
            <img :src="avatarUrl" role="presentation" />
        </span>
        <span>{{ formattedName }}</span>
    </div>
</template>

<script lang="ts">
import Vue from 'vue';
export default Vue.extend({
    props: {
        avatarUrl: {
            type: String,
            required: true,
        },
        formattedName: {
            type: String,
            required: true,
        },
        small: {
            type: Boolean,
            default: false,
        },
    },
});
</script>

<style scoped>
.studip-user-avatar {
    align-items: center;
    display: flex;
    gap: 0.25rem;
}
.studip-user-avatar-small img {
    width: 1em;
}
</style>
