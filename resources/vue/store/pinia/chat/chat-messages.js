import { ref, computed } from 'vue';
import { defineStore } from 'pinia';
import { api } from '../kitsu-api.js';
import { useSettingStore } from './chat-settings.js';

export const useMessageStore = defineStore('messages', () => {
    const settingStore = useSettingStore();

    const records = ref(new Map());
    const recordsByRoom = ref(new Map());
    const inProgress = ref(false);
    const errors = ref(false);

    function storeRecord(newRecord) {
        const roomId = newRecord.relationships.room.data.id; // todo is this correct?
        records.value.set(newRecord.id, newRecord);
        if (!recordsByRoom.value.has(roomId)) {
            recordsByRoom.value.set(roomId, []);
        }
        let messages = recordsByRoom.value.get(roomId);
        const existingIndex = messages.findIndex((m) => m.id === newRecord.id);
        if (existingIndex > -1) {
            messages[existingIndex] = newRecord;
        } else {
            messages.push(newRecord);
            messages.sort((a, b) => new Date(a.attributes.created_at) - new Date(b.attributes.created_at));
        }
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

    function byRoomId(roomId) {
        return recordsByRoom.value.get(roomId) || [];
    }

    const messagesInSelectedRoom = computed(() => {
        if (!settingStore.selectedRoom) {
            return [];
        }
        return byRoomId(settingStore.selectedRoom.id);
    });

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
        messagesInSelectedRoom,
        all,

        byId,
        byRoomId,
        clearRecords,
        fetchById,
        storeRecord,
    };
});
