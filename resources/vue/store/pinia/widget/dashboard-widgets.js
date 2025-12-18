import { ref, computed } from 'vue';
import { defineStore } from 'pinia';
import { api } from '../kitsu-api.js';

export const useWidgetStore = defineStore('widgetStore', () => {
    const records = ref(new Map());
    const isLoading = ref(false);
    const errors = ref(false);

    function storeRecord(newRecord) {
        records.value.set(newRecord.id, newRecord);
    }

    function removeRecord(recordId) {
        records.value.delete(recordId);
    }

    function clearRecords() {
        records.value = new Map();
    }

    const all = computed(() => {
        return [...records.value.values()];
    });

    function byId(id) {
        return records.value.get(String(id)); // we need that cast for unsigned ids
    }

    async function fetchById(id) {
        isLoading.value = true;
        try {
            const { data } = await api.fetch(`dashboard-widgets/${id}`);
            storeRecord(data);
        } catch (err) {
            console.error('fetching dashboard-widgets', err);
            errors.value = err;
        }
        isLoading.value = false;
    }

    async function updateWidgetPayload(widgetId, newPayload) {
        try {
            await api.patch('dashboard-widgets', {
                id: widgetId,
                payload: newPayload,
            });
            fetchById(widgetId);
        } catch (error) {
            console.error('Fehler beim Aktualisieren des Widget-Payloads:', error);
            throw error;
        }
    }

    return {
        records,
        removeRecord,
        clearRecords,
        storeRecord,
        isLoading,
        errors,
        all,
        byId,
        fetchById,

        updateWidgetPayload,
    };
});
