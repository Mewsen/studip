<template>
    <div class="studip-dual-list-box">
        <header v-if="showSearch" class="global-search-header">
            <slot name="search-input">
                <studip-search-input v-model="searchTerm" :placeholder="$gettext('Suchen / Filtern')" />
            </slot>
        </header>

        <div class="dual-list-columns">
            <div class="list-column">
                <div class="column-title">
                    <slot name="available-header">{{ availableTitle }}</slot>
                </div>

                <div class="list-container">
                    <slot name="available-list-overlay"></slot>
                    <ul
                        class="item-list"
                        role="listbox"
                        tabindex="0"
                        ref="availableListRef"
                        :aria-activedescendant="activeAvailableId"
                        @keydown="handleAvailableListKeydown"
                        @focus="handleAvailableFocus"
                        @mousemove="handleAvailableMouseMove"
                    >
                        <li
                            v-for="(item, index) in availableFiltered"
                            :key="getItemId(item)"
                            :id="`avail-${getItemId(item)}`"
                            role="option"
                            class="list-item selectable"
                            :aria-selected="availableActiveIndex === index"
                            @click="
                                setAvailableActive(index);
                                addItem(item);
                            "
                        >
                            <slot name="available-item" :item="item">
                                <span class="item-label">{{ item[labelKey] }}</span>
                            </slot>
                            <studip-icon shape="arr_1right" size="16" />
                        </li>

                        <li v-if="availableFiltered.length === 0" class="empty-hint">
                            <slot name="available-empty-hint">{{ emptyAvailableHint }}</slot>
                        </li>
                    </ul>
                </div>

                <div class="column-footer">
                    <slot name="available-footer">
                        <button class="as-link" @click="addAll" :disabled="availableFiltered.length === 0">
                            {{ $gettext('Alle hinzufügen') }}
                        </button>
                    </slot>
                </div>
            </div>

            <div class="list-column">
                <div class="column-title">
                    <slot name="selected-header">{{ selectedTitle }} ({{ modelValue.length }})</slot>
                </div>

                <div class="list-container">
                    <ul
                        class="item-list"
                        role="listbox"
                        tabindex="0"
                        ref="selectedListRef"
                        :aria-activedescendant="activeSelectedId"
                        @keydown="handleSelectedListKeydown"
                        @focus="handleSelectedFocus"
                        @mousemove="handleSelectedMouseMove"
                    >
                        <li
                            v-for="(item, index) in selectedItems"
                            :key="getItemId(item)"
                            :id="`sel-${getItemId(item)}`"
                            role="option"
                            class="list-item selected clickable"
                            :aria-selected="selectedActiveIndex === index"
                            @click="
                                setSelectedActive(index);
                                removeItem(getItemId(item));
                            "
                        >
                            <studip-icon shape="arr_1left" size="16" class="action-icon" v-if="!sortable" />

                            <div class="selected-item-wrapper">
                                <slot name="selected-item" :item="item" :index="index">
                                    <div class="item-label">{{ item[labelKey] }}</div>
                                </slot>
                            </div>

                            <div v-if="sortable" class="item-controls" @click.stop>
                                <button
                                    @click="moveItemUp(index)"
                                    :disabled="index === 0"
                                    class="control-btn"
                                    :title="$gettext('Nach oben')"
                                >
                                    <studip-icon shape="arr_1up" size="16" />
                                </button>
                                <button
                                    @click="moveItemDown(index)"
                                    :disabled="index === selectedItems.length - 1"
                                    class="control-btn"
                                    :title="$gettext('Nach unten')"
                                >
                                    <studip-icon shape="arr_1down" size="16" />
                                </button>
                            </div>
                        </li>

                        <li v-if="selectedItems.length === 0" class="empty-hint">
                            <slot name="selected-empty-hint">{{ emptySelectedHint }}</slot>
                        </li>
                    </ul>
                </div>

                <div class="column-footer">
                    <slot name="selected-footer">
                        <button class="as-link" @click="removeAll" :disabled="modelValue.length === 0">
                            {{ $gettext('Alle entfernen') }}
                        </button>
                    </slot>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, nextTick, onMounted, onUnmounted } from 'vue';

const props = defineProps({
    modelValue: { type: Array, default: () => [] },
    availableItems: { type: Array, default: () => [] },
    idKey: { type: String, default: 'id' },
    labelKey: { type: String, default: 'name' },
    availableTitle: { type: String, default: '' },
    selectedTitle: { type: String, default: '' },
    emptyAvailableHint: { type: String, default: 'Keine Einträge vorhanden' },
    emptySelectedHint: { type: String, default: 'Nichts ausgewählt' },
    sortable: { type: Boolean, default: true },
    removable: { type: Boolean, default: true },
    showSearch: { type: Boolean, default: true },
});

const emit = defineEmits(['update:modelValue']);

const searchTerm = ref('');
const isKeyboardMode = ref(false);
const availableActiveIndex = ref(-1);
const selectedActiveIndex = ref(-1);
const availableListRef = ref(null);
const selectedListRef = ref(null);

// Helpers
const getItemId = (item) => (item && typeof item === 'object' ? item[props.idKey] : item);
const isObjectMode = computed(() => props.modelValue.length > 0 && typeof props.modelValue[0] === 'object');

const selectedItems = computed(() => {
    if (isObjectMode.value) return props.modelValue;
    const map = new Map(props.availableItems.map((i) => [i[props.idKey], i]));
    return props.modelValue.map((id) => map.get(id)).filter(Boolean);
});

const availableFiltered = computed(() => {
    const selectedIdsSet = new Set(props.modelValue.map((item) => getItemId(item)));
    let list = props.availableItems.filter((item) => !selectedIdsSet.has(item[props.idKey]));

    if (searchTerm.value.trim() !== '') {
        const s = searchTerm.value.toLowerCase();
        list = list.filter((item) => (item[props.labelKey] || '').toLowerCase().includes(s));
    }
    return list;
});

const activeAvailableId = computed(() => {
    const item = availableFiltered.value[availableActiveIndex.value];
    return item ? `avail-${item[props.idKey]}` : null;
});

const activeSelectedId = computed(() => {
    const item = selectedItems.value[selectedActiveIndex.value];
    return item ? `sel-${getItemId(item)}` : null;
});

const addItem = (item) => {
    if (!item) return;
    const id = item[props.idKey];
    const currentIds = props.modelValue.map((i) => getItemId(i));
    if (!currentIds.includes(id)) {
        emit('update:modelValue', [...props.modelValue, isObjectMode.value ? item : id]);
    }
};

const removeItem = (id) => {
    const index = props.modelValue.findIndex((i) => getItemId(i) === id);
    emit(
        'update:modelValue',
        props.modelValue.filter((i) => getItemId(i) !== id)
    );
    nextTick(() => {
        if (selectedItems.value.length === 0) {
            selectedActiveIndex.value = -1;
            focusAvailableList();
        } else {
            selectedActiveIndex.value = Math.min(index, selectedItems.value.length - 1);
        }
    });
};

const addAll = () => {
    const toAdd = isObjectMode.value ? availableFiltered.value : availableFiltered.value.map((i) => i[props.idKey]);
    emit('update:modelValue', [...props.modelValue, ...toAdd]);
};
const removeAll = () => emit('update:modelValue', []);

const moveItemUp = (idx) => {
    if (idx <= 0) return;
    const arr = [...props.modelValue];
    [arr[idx - 1], arr[idx]] = [arr[idx], arr[idx - 1]];
    emit('update:modelValue', arr);
    selectedActiveIndex.value = idx - 1;
};

const moveItemDown = (idx) => {
    if (idx >= props.modelValue.length - 1) return;
    const arr = [...props.modelValue];
    [arr[idx], arr[idx + 1]] = [arr[idx + 1], arr[idx]];
    emit('update:modelValue', arr);
    selectedActiveIndex.value = idx + 1;
};

const focusAvailableList = () => availableListRef.value?.focus();
const focusSelectedList = () => selectedListRef.value?.focus();

const handleAvailableFocus = () => {
    if (isKeyboardMode.value && availableActiveIndex.value === -1 && availableFiltered.value.length > 0) {
        availableActiveIndex.value = 0;
    }
};

const handleSelectedFocus = () => {
    if (isKeyboardMode.value && selectedActiveIndex.value === -1 && selectedItems.value.length > 0) {
        selectedActiveIndex.value = 0;
    }
};

const handleAvailableMouseMove = () => {
    if (!isKeyboardMode.value) availableActiveIndex.value = -1;
};
const handleSelectedMouseMove = () => {
    if (!isKeyboardMode.value) selectedActiveIndex.value = -1;
};

const setAvailableActive = (i) => (availableActiveIndex.value = i);
const setSelectedActive = (i) => (selectedActiveIndex.value = i);

const onKeyDownGlobal = () => (isKeyboardMode.value = true);
const onMouseDownGlobal = () => (isKeyboardMode.value = false);

onMounted(() => {
    window.addEventListener('keydown', onKeyDownGlobal);
    window.addEventListener('mousedown', onMouseDownGlobal);
});
onUnmounted(() => {
    window.removeEventListener('keydown', onKeyDownGlobal);
    window.removeEventListener('mousedown', onMouseDownGlobal);
});

function handleAvailableListKeydown(event) {
    if (!availableFiltered.value.length) return;
    switch (event.key) {
        case 'ArrowDown':
            event.preventDefault();
            availableActiveIndex.value = Math.min(availableActiveIndex.value + 1, availableFiltered.value.length - 1);
            scrollActiveIntoView(activeAvailableId.value);
            break;
        case 'ArrowUp':
            event.preventDefault();
            availableActiveIndex.value = Math.max(0, availableActiveIndex.value - 1);
            scrollActiveIntoView(activeAvailableId.value);
            break;
        case 'Enter':
        case ' ':
            event.preventDefault();
            addItem(availableFiltered.value[availableActiveIndex.value]);
            break;
        case 'ArrowRight':
            event.preventDefault();
            if (selectedItems.value.length) focusSelectedList();
            break;
    }
}

function handleSelectedListKeydown(event) {
    if (!selectedItems.value.length) return;
    switch (event.key) {
        case 'ArrowDown':
            event.preventDefault();
            if (event.shiftKey && props.sortable) moveItemDown(selectedActiveIndex.value);
            else selectedActiveIndex.value = Math.min(selectedActiveIndex.value + 1, selectedItems.value.length - 1);
            scrollActiveIntoView(activeSelectedId.value);
            break;
        case 'ArrowUp':
            event.preventDefault();
            if (event.shiftKey && props.sortable) moveItemUp(selectedActiveIndex.value);
            else selectedActiveIndex.value = Math.max(0, selectedActiveIndex.value - 1);
            scrollActiveIntoView(activeSelectedId.value);
            break;
        case 'Enter':
        case ' ':
        case 'Delete':
        case 'Backspace': {
            event.preventDefault();
            const item = selectedItems.value[selectedActiveIndex.value];
            if (item) {
                removeItem(getItemId(item));
            }
            break;
        }
        case 'ArrowLeft':
            event.preventDefault();
            focusAvailableList();
            break;
    }
}

function scrollActiveIntoView(id) {
    if (!id) return;
    nextTick(() => {
        const el = document.getElementById(id);
        if (el) el.scrollIntoView({ block: 'nearest' });
    });
}
</script>

<style lang="scss" scoped>
.studip-dual-list-box {
    .global-search-header {
        margin-bottom: 15px;
    }

    .dual-list-columns {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 15px;
    }

    .column-title {
        color: var(--color--font-secondary);
        margin-bottom: 5px;
        min-height: 1.2em;
    }

    .list-container {
        position: relative;
        border: 1px solid var(--color--content-box-border);
        height: 300px;
        overflow-y: auto;
        background: var(--color--global-background);
    }

    .item-list {
        list-style: none;
        padding: 0;
        margin: 0;
        &:focus {
            outline: none;
        }
    }

    .list-item {
        display: flex;
        align-items: center;
        padding: 10px;
        cursor: pointer;
        border-bottom: 1px solid var(--color--divider);
        transition: background 0.1s;

        &:hover {
            background: var(--color--action-menu-hover);
        }

        &.selectable {
            justify-content: space-between;
        }

        &.selected {
            .studip-icon:first-child {
                margin-right: 10px;
            }
        }

        .selected-item-wrapper {
            flex-grow: 1;
        }
    }

    .item-label {
        flex: 1;
        min-width: 0;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .item-controls {
        display: flex;
        align-items: center;
        gap: 2px;
    }

    .control-btn {
        background: none;
        border: none;
        padding: 0 4px;
        cursor: pointer;
        &:disabled {
            opacity: 0.3;
            cursor: not-allowed;
        }
    }

    .column-footer {
        margin-top: 5px;
        button.as-link:disabled {
            color: var(--color--font-inactive);
            cursor: default;
            text-decoration: none;
        }
    }

    .empty-hint {
        padding: 15px;
        color: var(--color--font-secondary);
    }

    .list-item[aria-selected='true'] {
        background: var(--color--action-menu-hover);
        outline: 1px solid var(--color--highlight);
    }
}
</style>
