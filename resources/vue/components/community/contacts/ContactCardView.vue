<template>
    <div class="contact-card-grid">
        <div v-for="contact in data" 
             :key="contact.id" 
             class="contact-card"
             :class="{ 'is-selected': isItemSelected(contact.id) }"
        >
            <div v-if="isSelectionMode" class="contact-card__checkbox">
                <input 
                    type="checkbox" 
                    :checked="isItemSelected(contact.id)" 
                    @change="toggleItem(contact.id)" 
                />
            </div>

            <div class="contact-avatar">
                <img :src="contact.meta.avatar.medium" alt="" />
            </div>
            <div class="contact-info">
                <div class="contact-name">{{ contact['formatted-name'] }}</div>
                <div class="contact-username">{{ contact.username }}</div>
            </div>
            <div class="contact-actions">
                <studip-icon shape="mail" :size="16" /> {{ contact.email }}
            </div>
        </div>
    </div>
</template>

<script setup>
import { inject } from 'vue';

defineProps(['data', 'headers']);

// Wir holen uns alles aus dem Context
const { isSelectionMode, selectedIds, toggleItem } = inject('selectionContext');

// Hilfsfunktion um zu prüfen, ob eine ID in der Liste ist
const isItemSelected = (id) => {
    return selectedIds.value.includes(id);
};
</script>

<style scoped lang="scss">
.contact-card-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 20px;
    padding: 20px 0;
}
.contact-card {
    border: 1px solid var(--color--gray-lighter);
    background: #fff;
    padding: 15px;
    display: flex;
    align-items: center;
    gap: 12px;
    &:hover {
        border-color: var(--color--highlight);
    }
}
</style>
