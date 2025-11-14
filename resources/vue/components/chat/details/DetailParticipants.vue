<template>
    <ol class="detail-participants-list">
        <participant-element v-for="participant in participations" :key="participant.id" :participant="participant" />
    </ol>
</template>
<script setup>
import { computed } from 'vue';
import { useSettingStore } from '@/vue/store/pinia/chat/chat-settings.js';
import ParticipantElement from '@/vue/components/chat/details/ParticipantElement.vue';

const settingStore = useSettingStore();

const participations = computed(() => {
    const room = settingStore.selectedRoom;
    if (room) {
        return room.participations.data || [];
    }
    return [];
});
</script>
<style lang="scss">
.detail-participants-list {
    list-style: none;
    margin: 0;
    padding: 0;
}
</style>
