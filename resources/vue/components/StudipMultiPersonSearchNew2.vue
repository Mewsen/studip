<template>
    <div class="studip-multi-person-search">
        <div class="search-header">
            <studip-search-input
                v-model="searchTerm"
                :placeholder="$gettext('Nach Personen suchen... (mind. 3 Zeichen)')"
            />
        </div>

        <div class="mps-dual-list">
            <div class="mps-column">
                <div class="column-header">{{ $gettext('Suchergebnisse') }}</div>
                <div class="list-container">
                    <studip-progress-indicator v-if="showLoading" size="small" />
                    <template v-else>
                        <div
                            v-for="user in searchResults"
                            :key="user.id"
                            class="user-item selectable"
                            @click="selectUser(user)"
                            :title="$gettext('Hinzufügen')"
                        >
                            <img :src="user.avatar" class="avatar-small" alt="" />
                            <div class="user-info">
                                <span class="name">{{ user['formatted-name'] }}</span>
                                <span class="details">{{ user.perm }} ({{ user.username }})</span>
                            </div>
                            <studip-icon shape="arr_1right" size="16" />
                        </div>

                        <div v-if="searchResults.length === 0 && !isProcessing" class="empty-hint">
                            {{
                                searchTerm.length < 3
                                    ? $gettext('Geben Sie mindestens 3 Zeichen ein')
                                    : $gettext('Keine Ergebnisse gefunden')
                            }}
                        </div>
                    </template>
                </div>
                <button class="as-link" @click="selectAll" :disabled="searchResults.length === 0">
                    {{ $gettext('Alle hinzufügen') }}
                </button>
            </div>

            <div class="mps-column">
                <div class="column-header">
                    {{
                        $ngettext(
                            'Sie haben eine Person ausgewählt',
                            'Sie haben %{ count } Personen ausgewählt',
                            selectedUsers.length,
                            { count: selectedUsers.length }
                        )
                    }}
                </div>
                <div class="list-container">
                    <div
                        v-for="user in selectedUsers"
                        :key="user.id"
                        class="user-item selected"
                        @click="deselectUser(user)"
                        :title="$gettext('Entfernen')"
                    >
                        <studip-icon shape="arr_1left" size="16" />
                        <img :src="user.avatar" class="avatar-small" alt="" />
                        <div class="user-info">
                            <span class="name">{{ user['formatted-name'] }}</span>
                            <span class="details">{{ user.perm }} ({{ user.username }})</span>
                        </div>
                    </div>
                </div>
                <button class="as-link" @click="deselectAllUsers" :disabled="selectedUsers.length === 0">
                    {{ $gettext('Alle entfernen') }}
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, watch, onUnmounted } from 'vue';
import { useLoadingBuffer } from '@/vue/composables/useLoadingBuffer.js';
import StudipSearchInput from './StudipSearchInput.vue';
import debounce from 'lodash/debounce';

const props = defineProps({
    modelValue: { type: Array, default: () => [] },
    searchContext: { type: String, required: true },
    exclude: { type: Array, default: () => [] },
});

const emit = defineEmits(['update:modelValue']);

const searchTerm = ref('');
const searchResults = ref([]);
const selectedUsers = ref([...props.modelValue]);

const { showLoading, isProcessing, runWithLoading } = useLoadingBuffer(500);

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

            searchResults.value = data.filter((item) => {
                const isGarbage = item.id === '--';
                const isExcluded = props.exclude.includes(item.id);
                const isAlreadySelected = selectedUsers.value.some((s) => s.id === item.id);

                return !isGarbage && !isExcluded && !isAlreadySelected;
            });
        } catch (e) {
            console.error('MPS Search failed', e);
        }
    });
};

const debouncedSearch = debounce(performSearch, 300);

watch(searchTerm, () => {
    debouncedSearch();
});

const selectUser = (user) => {
    selectedUsers.value.push(user);
    searchResults.value = searchResults.value.filter((u) => u.id !== user.id);
    emit('update:modelValue', selectedUsers.value);
};

const deselectUser = (user) => {
    selectedUsers.value = selectedUsers.value.filter((u) => u.id !== user.id);

    const matchesSearch =
        user.text?.toLowerCase().includes(searchTerm.value.toLowerCase()) ||
        user.name?.toLowerCase().includes(searchTerm.value.toLowerCase());

    if (matchesSearch && !searchResults.value.some((s) => s.id === user.id)) {
        searchResults.value.push(user);
    }

    emit('update:modelValue', selectedUsers.value);
};

const deselectAllUsers = () => {
    const toRemove = [...selectedUsers.value];

    toRemove.forEach((user) => {
        deselectUser(user);
    });

    selectedUsers.value = [];
    emit('update:modelValue', []);
};

const selectAll = () => {
    selectedUsers.value.push(...searchResults.value);
    searchResults.value = [];
    emit('update:modelValue', selectedUsers.value);
};

onUnmounted(() => {
    debouncedSearch.cancel();
});
</script>

<style lang="scss" scoped>
.studip-multi-person-search {
    .search-header {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 15px;
        .search-status {
            width: 20px;
            height: 20px;
        }
    }

    .mps-dual-list {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 15px;

        .column-header {
            color: var(--color--font-secondary);
        }

        .list-container {
            border: 1px solid var(--color--content-box-border);
            height: 300px;
            overflow-y: auto;
            background: var(--color--global-background);
        }

        .user-item {
            display: flex;
            align-items: center;
            padding: 15px 10px;
            cursor: pointer;
            border-bottom: 1px solid var(--color--divider);
            transition: background 0.1s;

            &:hover {
                background: var(--color--action-menu-hover);
            }

            &.selected {
                .studip-icon {
                    margin-right: 10px;
                }
            }

            .avatar-small {
                width: 32px;
                height: 32px;
                border-radius: 50%;
                object-fit: cover;
                margin-right: 10px;
            }

            .user-info {
                flex: 1;
                overflow: hidden;
                .name {
                    display: block;
                    font-weight: bold;
                    white-space: nowrap;
                    overflow: hidden;
                    text-overflow: ellipsis;
                }
                .details {
                    display: block;
                    color: var(--color--font-secondary);
                }
            }
        }

        .empty-hint {
            padding: 0.5em;
        }
        button.as-link:disabled {
            color: var(--color--font-inactive);
        }
    }
}
</style>
