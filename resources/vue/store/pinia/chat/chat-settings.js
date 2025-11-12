import { ref } from 'vue';
import { defineStore } from 'pinia';

export const useSettingStore = defineStore(
    'settings',
    () => {
        const lastRoomId = ref(null);

        const roomFilter = ref('all');

        function setLastRoomId(id) {
            lastRoomId.value = id;
        }

        function setRoomFilter(filter) {
            roomFilter.value = filter;
        }


        return {
            lastRoomId,
            roomFilter,

            setLastRoomId,
            setRoomFilter,
        };
    }
);