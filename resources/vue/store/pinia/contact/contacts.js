import { ref, computed } from 'vue';
import { defineStore } from 'pinia';
import { api } from '../kitsu-api.js';
import { useContactGroupStore } from './contact-groups.js';

export const useContactStore = defineStore('contactStore', () => {
    const records = ref(new Map());
    const isLoading = ref(false);
    const errors = ref(false);

    const contactGroupStore = useContactGroupStore();

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
                    'page[limit]': 10000,
                },
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

    async function addContacts(ownerId, newContactIds) {
        isLoading.value = true;
        try {
            const payload = newContactIds.map((id) => ({
                type: 'users',
                id: id,
            }));

            await api.axios.post(`users/${ownerId}/relationships/contacts`, { data: payload });
            clearRecords();
            await fetchAll(ownerId);
            const groupId = contactGroupStore.selectedGroupId;
            if (groupId && groupId !== 'all') {
                await contactGroupStore.addMultipleUsersToGroup(groupId, newContactIds);
            }

            return true;
        } catch (err) {
            console.error('Fehler beim Hinzufügen der Kontakte:', err);
            throw err;
        } finally {
            isLoading.value = false;
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

    function assignGroupToContact(contactId, groupId) {
        const contact = records.value.get(String(contactId));
        if (contact) {
            if (!contact.group_ids) {
                contact.group_ids = new Set();
            }
            contact.group_ids.add(String(groupId));
        }
    }

    function removeGroupFromContact(contactId, groupId) {
        const contact = records.value.get(String(contactId));
        if (contact && contact.group_ids) {
            contact.group_ids.delete(String(groupId));
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
        addContacts,
        removeContact,
        assignGroupToContact,
        removeGroupFromContact,
    };
});
