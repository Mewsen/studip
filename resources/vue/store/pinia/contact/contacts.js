import { ref, computed } from 'vue';
import { defineStore } from 'pinia';
import { api } from '../kitsu-api.js';

export const useContactStore = defineStore('contactStore', () => {
    const records = ref(new Map());
    const isLoading = ref(false);
    const errors = ref(false);

    function storeRecord(newRecord) {
        const id = String(newRecord.id);
        records.value.set(id, newRecord);
    }

    function removeRecord(recordId) {
        records.value.delete(String(recordId));
    }

    function clearRecords() {
        records.value = new Map();
    }

    const all = computed(() => {
        return [...records.value.values()];
    });

    function byId(id) {
        return records.value.get(String(id));
    }

    async function fetchAll(ownerId) {
        isLoading.value = true;
        try {
            const { data } = await api.fetch(`users/${ownerId}/contacts`, {
            params: {
                'page[limit]': 10000
            }
        });
            data.forEach((contact) => {
                storeRecord(contact);
            });
        } catch (err) {
            console.error('fetching all contacts', err);
            errors.value = err;
        }
        isLoading.value = false;
    }

    async function addContact(ownerId, newContactId) {
        try {
            const { data } = await api.axios.post(`users/${ownerId}/relationships/contacts`, {
                data: [{ type: 'users', id: newContactId }],
            });
            console.log(data);
            storeRecord(data);
        } catch (err) {
            console.error('Fehler beim Hinzufügen der Kontakt-Beziehung:', err);
            throw err;
        }
    }

    async function removeContact(ownerId, contactId) {
        try {
            await api.axios.delete(`users/${ownerId}/relationships/contacts`, {
                data: {
                    data: [{ type: 'users', id: String(contactId) }],
                },
            });
            removeRecord(contactId);
        } catch (err) {
            console.error('Fehler beim Löschen der Kontakt-Beziehung:', err);
            throw err;
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
        fetchAll,
        addContact,
        removeContact,
    };
});
