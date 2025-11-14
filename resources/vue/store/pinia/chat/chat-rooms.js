import { ref, computed } from 'vue';
import { defineStore } from 'pinia';
import { api } from '../kitsu-api.js';

export const useRoomStore = defineStore('rooms', () => {
    const records = ref(new Map());
    const inProgress = ref(false);
    const errors = ref(false);

    function storeRecord(newRecord) {
        records.value.set(newRecord.id, newRecord);
    }
    function clearRecords() {
        records.value = new Map();
    }

    const all = computed(() => {
        return  [...records.value.values()];
    });

    function byId(id) {
        return records.value.get(id);
    }

    async function fetchById(id) {
        inProgress.value = true;
        try {
            const { data } = await api.fetch(`blubber-threads/${id}`, {
                params: {
                    include: 'participations',
                },
            });
            storeRecord(data);
        } catch (err) {
            console.error('fetching threads', err);
            errors.value = err;
        }
        inProgress.value = false;
    }

    async function fetchAll() {
        inProgress.value = true;
        try {
            const { data } = await api.fetch('blubber-threads', {
                params: {
                    include: 'participations',
                },
            });
            data.forEach((room) => {
                storeRecord(room);
            });
        } catch (err) {
            console.error('fetching threads', err);
            errors.value = err;
        }
        inProgress.value = false;
    }

    return {
        records,
        inProgress,
        errors,

        all,

        byId,
        fetchById,
        clearRecords,
        storeRecord,
        fetchAll,
    };
});
