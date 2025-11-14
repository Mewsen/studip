import { computed, ref } from 'vue';
import { defineStore } from 'pinia';
import { useRoomStore } from './chat-rooms.js';

export const useSettingStore = defineStore(
    'settings',
    () => {
        const roomStore = useRoomStore();

        const lastRoomId = ref(null);
        const roomFilter = ref('all');
        const selectedRoomId = ref(null);
        const showDetailsDrawer = ref(false);
        const detailsScope = ref('room');

        const selectedRoom = computed(() => {
            return roomStore.byId(selectedRoomId.value);
        });

        function setLastRoomId(id) {
            lastRoomId.value = id;
        }
        function setRoomFilter(filter) {
            roomFilter.value = filter;
        }
        function setSelectedRoomId(id) {
            selectedRoomId.value = id;
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
            selectedRoomId,
            showDetailsDrawer,

            selectedRoom,

            setDetailsScope,
            setLastRoomId,
            setRoomFilter,
            setSelectedRoomId,
            setDetailsDrawer,
        };
    }
);