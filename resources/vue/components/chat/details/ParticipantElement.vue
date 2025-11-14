<template>
    <user-avatar-dropdown
        :user="{
            id: participant.id,
            avatar_url: participant.meta.avatar.small,
            username: participant.attributes['username'],
            name: participantName,
        }"
    />
    <a :href="userProfile(participant)">
        {{ participantName }}
    </a>
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
    return props.participant.attributes['formatted-name'];
});

const userProfile = (user) => {
    const username = user.attributes.username;
    return window.STUDIP.URLHelper.getURL('dispatch.php/profile', { username }, true);
};
</script>
