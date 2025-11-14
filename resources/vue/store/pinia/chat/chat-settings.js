import { ref } from 'vue';
import { defineStore } from 'pinia';
import { useRoomStore } from './chat-rooms.js';

export const useSettingStore = defineStore(
    'settings',
    () => {
        const roomStore = useRoomStore();

        const lastRoomId = ref(null);
        const roomFilter = ref('all');
        const selectedRoom = ref(null);
        const showDetailsDrawer = ref(false);
        const detailsScope = ref('room');

        function setLastRoomId(id) {
            lastRoomId.value = id;
        }
        function setRoomFilter(filter) {
            roomFilter.value = filter;
        }
        function setSelectedRoom(room) {
            selectedRoom.value = room;
        }
        function setSelectedRoomById(id) {
            const room = roomStore.byId(id);
            if (room) {
                selectedRoom.value = room;
            }
        }
        function setDetailsDrawer(bool) {
            showDetailsDrawer.value = bool;
        }
        function setDetailsScope(scope) {
            detailsScope.value = scope;
            showDetailsDrawer.value = true;
        }

        return {
            detailsScope,
            lastRoomId,
            roomFilter,
            selectedRoom,
            showDetailsDrawer,

            setDetailsScope,
            setLastRoomId,
            setRoomFilter,
            setSelectedRoom,
            setSelectedRoomById,
            setDetailsDrawer,
        };
    }
);