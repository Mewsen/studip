<template>
    <li class="detail-participant-element">
        <user-avatar-dropdown
            size="30px"
            :user="{
                id: participant.id,
                avatar_url: participant.meta.avatar.small,
                username: username,
                name: participantName,
            }"
        />
        <a :href="userProfile">
            {{ participantName }}
        </a>
    </li>
</template>
<script setup>
import { computed } from 'vue';
import UserAvatarDropdown from '@/vue/components/avatar/UserAvatarDropdown.vue';

const props = defineProps({
    participant: {
        type: Object,
        required: true,
    },
});

const participantName = computed(() => {
    return props.participant['formatted-name'];
});

const username = computed(() => {
    return props.participant.username;
});

const userProfile = computed(() => {
    return window.STUDIP.URLHelper.getURL('dispatch.php/profile', { username }, true);
});
</script>

<style lang="scss">
.detail-participant-element {
    display: flex;
    align-items: center;
    padding: 8px 12px;
    border-bottom: solid thin var(--color--divider);

    &:last-child {
        border-bottom: none;
    }

    .user-avatar-dropdown {
        margin-right: 10px;
    }
}</style>
