<template>
    <div class="table-view-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th v-for="key in columnKeys" :key="key">{{ formatKey(key) }}</th>
                    <th class="table-action-header">Details</th>
                </tr>
            </thead>

            <tbody>
                <tr v-for="(item, index) in data" :key="index">
                    <slot name="table-layout-row" :item="item">
                        <td v-for="key in columnKeys" :key="key" :data-label="formatKey(key)">
                            {{ item[key] }}
                        </td>
                    </slot>

                    <td class="table-action-cell">
                        <button class="detail-button" @click="emit('showDetails', item)">i</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>
<script setup>
import { defineProps, defineEmits, computed } from 'vue';

const props = defineProps({
    data: { type: Array, required: true },
    titleKey: { type: String, required: true },
});

const emit = defineEmits(['showDetails']); // Event, um den Drawer im DataSetViewer zu öffnen

const columnKeys = computed(() => {
    if (props.data.length === 0) return [];
    return Object.keys(props.data[0]);
});

const formatKey = (key) => {
    return key.charAt(0).toUpperCase() + key.slice(1);
};
</script>

<style scoped>
.table-view-container {
    overflow-x: auto;
}

.data-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.95em;
}

.data-table th,
.data-table td {
    padding: 10px 12px;
    text-align: left;
    border-bottom: 1px solid #ddd;
}

.data-table thead th {
    background-color: #f5f5f5;
    color: #333;
    font-weight: bold;
}

/* Aktion-Spalte */
.table-action-header {
    text-align: center;
}
.table-action-cell {
    text-align: center;
}

.detail-button {
    background: #007bff;
    color: white;
    border: none;
    border-radius: 50%;
    width: 28px;
    height: 28px;
    cursor: pointer;
    font-weight: bold;
    line-height: 1;
}

@media screen and (max-width: 768px) {
    .table-action-header,
    .table-action-cell {
    }
}
</style>
