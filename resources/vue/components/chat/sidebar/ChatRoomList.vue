<template>
    <ol class="chat-room-list">
        <li v-for="room in chatRooms" :key="room.id">
            <room-element :room="room" />
        </li>
    </ol>
</template>

<script setup>
import { computed } from 'vue';
import { useRoomStore } from '@/vue/store/pinia/chat/chat-rooms.js';
import RoomElement from '@/vue/components/chat/sidebar/roomlist/RoomElement.vue';

const props = defineProps({
    // Define props if needed
});

const roomsStore = useRoomStore();

const chatRooms = computed(() => {
    return roomsStore.all || [];
});

</script>
<style lang="scss" scoped>
.chat-room-list {
    list-style: none;
    margin: 0;
    padding: 0;

    li {
        margin-bottom: 10px;
    }
}
</style>