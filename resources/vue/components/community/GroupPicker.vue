<template>
    <div class="group-picker-container">
        <div v-if="loading" class="loading-state">Lade verfügbare Interessengruppen...</div>

        <StudipDualListBox
            v-else
            v-model="selectedGroupIds"
            :available-items="allGroups"
            label-key="name"
            id-key="group_uuid"
            :removable="true"
            :sortable="false"
        >
            <template #available-item="{ item, displayKey }">
                <div class="item-content-fancy">
                    <span class="chat-icon">
                        👥
                    </span>
                    <span class="item-label">{{ item[displayKey] }}</span>
                </div>
            </template>

            <template #selected-item="{ item, displayKey }">
                <div class="item-content-fancy">
                    <div class="item-label">{{ item[displayKey] }}</div>
                </div>
            </template>
        </StudipDualListBox>
    </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import StudipDualListBox from '@/vue/components/StudipDualListBox.vue';

// 1. Definiere den spezifischen Typ für die Backend-Daten der Gruppe
interface GroupItem {
    group_uuid: string; // Wird als ID verwendet
    name: string; // Wird als anzuzeigender Name verwendet
    member_count: number;
    // ... weitere gruppenspezifische Felder
}

// 2. Props und Emits (Muss das Array der IDs über v-model erhalten)
const props = defineProps<{
    modelValue: string[]; // Das Array der IDs der ausgewählten Gruppen
}>();
const emit = defineEmits(['update:modelValue']);

// Bidirektionales Binding für die ausgewählten IDs
const selectedGroupIds = computed({
    get: () => props.modelValue,
    set: (value: string[]) => emit('update:modelValue', value),
});

// 3. Daten laden (Simulierte Backend-Anbindung)
const allGroups = ref<GroupItem[]>([]);
const loading = ref(true);

// 🚨 ACHTUNG: Dies muss durch Ihren tatsächlichen API-Aufruf ersetzt werden!
async function loadGroups() {
    loading.value = true;

    // Beispiel für simulierte Backend-Daten
    // In der echten Anwendung: fetch('/api/interest-groups?filter=member_only')
    await new Promise((resolve) => setTimeout(resolve, 500));

    allGroups.value = [
        { group_uuid: 'a1b2c3d4', name: 'Vue.js Entwicklung', member_count: 45 },
        { group_uuid: 'e5f6g7h8', name: 'Data Science Treff', member_count: 120 },
        { group_uuid: 'i9j0k1l2', name: 'Open Source Community', member_count: 80 },
        { group_uuid: 'm3n4o5p6', name: 'UX/UI Design Workshop', member_count: 30 },
        { group_uuid: 'q7r8s9t0', name: 'E-Learning Tools Beta', member_count: 15 },
        { group_uuid: '1120001', name: 'Foo Bar 1', member_count: 0 },
        { group_uuid: '1120002', name: 'Foo Bar 2', member_count: 0 },
        { group_uuid: '1120003', name: 'Foo Bar 3', member_count: 0 },
        { group_uuid: '1120004', name: 'Foo Bar 4', member_count: 0 },
        { group_uuid: '1120005', name: 'Foo Bar 5', member_count: 0 },
        { group_uuid: '1120006', name: 'Foo Bar 6', member_count: 0 },
        { group_uuid: '1120007', name: 'Foo Bar 7', member_count: 0 },
        { group_uuid: '1120008', name: 'Foo Bar 8', member_count: 0 },
    ];

    loading.value = false;
}

loadGroups();
</script>

<style scoped>
.group-picker-container {
    min-height: 380px; /* Hält Platz für die DualListBox, während geladen wird */
}
.loading-state {
    text-align: center;
    padding: 100px 0;
    color: #888;
}
</style>
