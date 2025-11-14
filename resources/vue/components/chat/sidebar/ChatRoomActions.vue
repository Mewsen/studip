<template>
    <div class="chat-room-actions">
        <studip-search v-model:search="searchQuery" @submit="onSearchSubmit" @reset="onSearchReset" />
        <div class="chat-room-actions-menus">
            <studip-context-menu :title="$gettext('Räume filtern')" button-shape="filter2">
                <template #content> testing... </template>
            </studip-context-menu>
            <studip-context-menu :title="$gettext('Raum hinzufügen')" button-shape="add">
                <template #content> testing... </template>
            </studip-context-menu>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import StudipContextMenu from '@/vue/components/StudipContextMenu.vue';
import StudipSearch from '@/vue/components/StudipSearch.vue';

const searchQuery = ref('');
function onSearchSubmit() {
    console.log('Search submitted with query:', searchQuery.value);
}
function onSearchReset() {
    searchQuery.value = '';
    console.log('Search reset');
}
</script>
<style lang="scss" scoped>
.chat-room-actions {
    display: flex;
    align-items: center;
    gap: 10px;
    width: 100%;
    margin-bottom: 2em;

    .studip-search {
        flex-grow: 1;
        flex-shrink: 1;
        flex-basis: auto;
        margin-bottom: 0;
        height: 32px;
    }
    .chat-room-actions-menus {
        display: flex;
        gap: 5px;
        flex-shrink: 0;
    }
}
</style>
