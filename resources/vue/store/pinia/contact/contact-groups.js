import { ref, computed } from 'vue';
import { defineStore } from 'pinia';
import { api } from '../kitsu-api.js';
import { useContactStore } from './contacts.js';

export const useContactGroupStore = defineStore('contactGroupStore', () => {
    const records = ref(new Map());
    const isLoading = ref(false);
    const errors = ref(false);

    function storeRecord(newRecord) {
        const id = String(newRecord.id);
        records.value.set(id, { ...newRecord, members_loaded: false });
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

    async function fetchAll() {
        isLoading.value = true;
        try {
            const { data } = await api.fetch(`user-contact-groups`, {
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

    async function fetchById(contactGroupId) {
    try {
        const { data } = await api.get(`user-contact-groups/${contactGroupId}`);
        storeRecord(data);
        return data;
    } catch (err) {
        console.error(`Fehler beim Laden der Kontakt-Gruppe ${contactGroupId}:`, err);
        throw err;
    }
}

    async function addContactGroup(newGroupName) {
        let state = false;
        try {
            const { data } = await api.post(`user-contact-groups`, {
                name: newGroupName,
            });
            storeRecord(data);
            state = true;
        } catch (err) {
            console.error('Fehler beim Hinzufügen der Kontakt-Gruppe:', err);
            throw err;
        }

        return state;
    }

    async function updateContactGroup(contactGroupId, updatedName) {
    let state = false;
    try {
        const { data } = await api.patch(`user-contact-groups`, {
            type: 'contact-groups',
            id: contactGroupId,
            name: updatedName,
        });
        storeRecord(data);
        state = true;
    } catch (err) {
        console.error(`Fehler beim Updaten der Kontakt-Gruppe ${contactGroupId}:`, err);
        throw err;
    }

    return state;
}

    async function removeContactGroup(contactGroupId) {
        let state = false;
        try {
            await api.axios.delete(`user-contact-groups/${contactGroupId}`);
            removeRecord(contactGroupId);
            state = true;
        } catch (err) {
            console.error('Fehler beim Löschen der Kontakt-Gruppe:', err);
            throw err;
        }

        return state;
    }

    async function fetchGroupMembers(groupId) {
        const contactStore = useContactStore();
        try {
            const { data } = await api.fetch(`user-contact-groups/${groupId}/relationships/group-users`);

            data.forEach((item) => {
                contactStore.assignGroupToContact(item.id, groupId);
            });

            const group = byId(groupId);
            if (group) group.members_loaded = true;
        } catch (err) {
            console.error('Error syncing group members', err);
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
        fetchById,
        addContactGroup,
        removeContactGroup,
        updateContactGroup,
        fetchGroupMembers,
    };
});
