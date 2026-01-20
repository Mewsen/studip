<template>
    <div class="studip-multi-person-search">
        <studip-dual-list-box
            v-model="selectedUsers"
            :available-items="searchResults"
            label-key="formatted-name"
            id-key="id"
            :sortable="false"
        >
            <template #search-input>
                <studip-search-input
                    v-model="searchTerm"
                    :placeholder="$gettext('Nach Personen suchen... (mind. 3 Zeichen)')"
                />
            </template>

            <template #available-header>
                {{ $gettext('Suchergebnisse') }}
            </template>

            <template #selected-header>
                {{
                    $ngettext('Eine Person ausgewählt', '%{ count } Personen ausgewählt', selectedUsers.length, {
                        count: selectedUsers.length,
                    })
                }}
            </template>

            <template #available-item="{ item }">
                <div class="mps-user-tile">
                    <img :src="item.avatar" class="avatar-small" alt="" />
                    <div class="user-info">
                        <span class="name">{{ item['formatted-name'] }}</span>
                        <span class="details">{{ item.perm }} ({{ item.username }})</span>
                    </div>
                </div>
            </template>

            <template #selected-item="{ item }">
                <div class="mps-user-tile">
                    <img :src="item.avatar" class="avatar-small" alt="" />
                    <div class="user-info">
                        <span class="name">{{ item['formatted-name'] }}</span>
                        <span class="details">{{ item.perm }} ({{ item.username }})</span>
                    </div>
                </div>
            </template>

            <template #available-list-overlay v-if="showLoading">
                <div class="mps-overlay">
                    <studip-progress-indicator size="small" />
                </div>
            </template>

            <template #available-empty-hint>
                {{
                    searchTerm.length < 3
                        ? $gettext('Geben Sie mindestens 3 Zeichen ein')
                        : $gettext('Keine Ergebnisse gefunden')
                }}
            </template>
        </studip-dual-list-box>
    </div>
</template>

<script setup>
import { ref, watch, onUnmounted, computed } from 'vue';
import StudipDualListBox from './StudipDualListBox.vue';
import StudipSearchInput from './StudipSearchInput.vue';
import { useLoadingBuffer } from '@/vue/composables/useLoadingBuffer.js';
import debounce from 'lodash/debounce';

const emit = defineEmits(['update:modelValue']);

const props = defineProps({
    modelValue: { type: Array, default: () => [] },
    searchContext: { type: String, required: true },
    exclude: { type: Array, default: () => [] },
});

const debouncedSearch = debounce(performSearch, 300);
const { showLoading, runWithLoading } = useLoadingBuffer(500);

const searchTerm = ref('');
const searchResults = ref([]);
const selectedUsers = computed({
    get: () => props.modelValue,
    set: (val) => emit('update:modelValue', val),
});

watch(searchTerm, debouncedSearch);

const performSearch = async () => {
    const query = searchTerm.value.trim();
    if (query.length < 3) {
        searchResults.value = [];
        return;
    }
    await runWithLoading(async () => {
        try {
            const url = STUDIP.URLHelper.getURL(
                `dispatch.php/multipersonsearch/ajax_search_vue/${props.searchContext}`,
                { s: query }
            );
            const response = await fetch(url);
            const data = await response.json();
            searchResults.value = data.filter((item) => item.id !== '--' && !props.exclude.includes(item.id));
        } catch (e) {
            console.error('MPS Search failed', e);
        }
    });
};

onUnmounted(() => debouncedSearch.cancel());
</script>

<style lang="scss">
.mps-user-tile {
    display: flex;
    align-items: center;
    gap: 10px;
    .avatar-small {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        object-fit: cover;
    }
    .user-info {
        display: flex;
        flex-direction: column;
        line-height: 1.2;
        .name {
            font-weight: bold;
        }
        .details {
            font-size: 0.85em;
            color: var(--color--font-secondary);
        }
    }
}
.mps-overlay {
    position: absolute;
    inset: 0;
    background: rgba(255, 255, 255, 0.7);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 5;
}
</style>
