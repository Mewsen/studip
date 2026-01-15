import { ref, computed } from 'vue';
import { defineStore } from 'pinia';
import { api } from '../kitsu-api.js';

export const useContactStore = defineStore('contactStore', () => {
    const records = ref(new Map());
    const isLoading = ref(false);
    const errors = ref(false);

    function storeRecord(newRecord) {
        records.value.set(String(newRecord.id), newRecord);
    }

    function clearRecords() {
        records.value = new Map();
    }

    const all = computed(() => {
        void records.value.size;
        return  [...records.value.values()];
    });

    function removeRecord(contactId) {
        // records.value = records.value.filter(({ id }) => id !== contactId);
        records.value.delete(String(contactId));
    }

    function byId(id) {
        void records.value.size;
        return records.value.get(String(id)); // we need that cast for unsigned ids
    }

    async function fetchAll(userId) {
        isLoading.value = true;
        try {
            const { data } = await api.fetch(`users/${userId}/contacts`);
            data.forEach((contact => {
                storeRecord(contact);
            }))
        } catch (err) {
            console.error('fetching contacts', err);
            errors.value = err;
        }
        isLoading.value = false;
    }

    return {
        records,
        storeRecord,
        clearRecords,
        removeRecord,
        isLoading,
        errors,
        all,
        byId,
        fetchAll,
    };
});
