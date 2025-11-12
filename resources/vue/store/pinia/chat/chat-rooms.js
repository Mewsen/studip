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
        // return  [...records.value.values()];
//dummy data for now
        return [
            { id: 1, name: 'Global Chat', 'last-message-date': 1762957809, 'unread-count': 0 },
            { id: 2, name: 'Chat 123', 'last-message-date': 1762957809, 'unread-count': 0 },
            { id: 3, name: 'Informatik A', 'last-message-date': 1762957809, 'unread-count': 0 },
            { id: 4, name: 'Datenbanksysteme', 'last-message-date': 1762957809, 'unread-count': 0 },
            { id: 5, name: 'Foo Bar', 'last-message-date': 1762957809, 'unread-count': 42 },

        ];
    });

    function byId(id) {
        return records.value.get(id);
    }

    async function fetchById(id) {
        inProgress.value = true;
        try {
            const { data } = await api.fetch(`blubber-threads/${id}`, {
                params: {},
            });
            storeRecord(data);
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
    };
});
