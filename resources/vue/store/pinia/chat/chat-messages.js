import { ref, computed } from 'vue';
import { defineStore } from 'pinia';
import { api } from '../kitsu-api.js';

export const useMessageStore = defineStore('messages', () => {
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
        return [...records.value.values()];
    });

    function byId(id) {
        return records.value.get(id);
    }

    async function fetchById(id) {
        inProgress.value = true;
        try {
            const { data } = await api.fetch(`blubber-comments/${id}`, {
                params: {},
            });
            storeRecord(data);
        } catch (err) {
            console.error('fetching comments', err);
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
