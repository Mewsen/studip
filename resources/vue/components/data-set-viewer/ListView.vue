<template>
    <ul class="list-view-container">
        <li v-for="(item, index) in data" :key="index" class="data-list-item">
            <slot name="list-layout-item" :item="item">
                <div class="list-fallback">
                    <h4 class="list-title">
                        {{ item[titleKey] || `Titel-Feld '${titleKey}' fehlt` }}
                    </h4>

                    <ul class="list-details">
                        <li v-for="(value, key) in item" :key="key" class="default-row">
                            <template v-if="key !== titleKey">
                                <strong>{{ key.charAt(0).toUpperCase() + key.slice(1) }}:</strong> {{ value }}
                            </template>
                        </li>
                    </ul>
                </div>
            </slot>
        </li>
    </ul>
</template>

<script setup>
const props = defineProps({
    data: { type: Array, required: true },
    titleKey: { type: String, required: true },
});
</script>

<style scoped>
.list-view-container {
    list-style: none;
    padding: 0;
}

.data-list-item {
    border-bottom: 1px solid #eee;
    padding: 10px 0;
    margin-bottom: 0;
    cursor: pointer;
}

.data-list-item:hover {
    background-color: #f9f9f9;
}

.data-list-item:last-child {
    border-bottom: none;
}

.list-fallback {
    padding: 5px 15px;
}

.list-title {
    margin: 0;
    font-size: 1.1em;
    color: #0056b3;
}

.list-details {
    list-style: none;
    padding: 0;
    margin-top: 5px;
    font-size: 0.85em;
    color: #666;
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
}

.list-details li {
    padding: 0;
    margin: 0;
    border-bottom: none;
}
</style>
