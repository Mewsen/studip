<template>
    <div class="studip-dual-list-box">
        <!-- AVAILABLE LIST -->
        <section class="list-column available-list">
            <header class="list-header">
                <h4>{{ availableTitle }}</h4>
                <input
                    type="search"
                    v-model="searchTerm"
                    :placeholder="$gettext('Suchen / Filtern')"
                    class="list-search-input"
                />
            </header>

            <ul
                class="item-list"
                role="listbox"
                tabindex="0"
                :aria-activedescendant="activeAvailableId"
                @keydown="handleAvailableListKeydown"
                @focus="handleAvailableFocus"
                @mousemove="handleAvailableMouseMove"
            >
                <li
                    v-for="(item, index) in availableFiltered"
                    :key="item[identityKey]"
                    :id="`avail-${item[identityKey]}`"
                    role="option"
                    class="list-item"
                    :aria-selected="availableActiveIndex === index"
                    @click="
                        setAvailableActive(index);
                        addItem(item);
                    "
                >
                    <slot name="available-item" :item="item" :displayKey="displayKey" :identityKey="identityKey">
                        <span class="item-label">{{ item[displayKey] }}</span>
                    </slot>
                    <span class="icon-add" aria-hidden="true">
                        <studip-icon shape="add" />
                    </span>
                </li>

                <li v-if="availableFiltered.length === 0" class="no-results">
                    {{ emptyAvailableHint }}
                </li>
            </ul>
        </section>

        <!-- SELECTED LIST -->
        <section class="list-column selected-list">
            <header class="list-header">
                <h4>{{ selectedTitle }} ({{ selectedIds.length }})</h4>
            </header>

            <ul
                class="item-list"
                role="listbox"
                tabindex="0"
                :aria-activedescendant="activeSelectedId"
                @keydown="handleSelectedListKeydown"
                @focus="handleSelectedFocus"
                @mousemove="handleSelectedMouseMove"
            >
                <li
                    v-for="(item, index) in selectedItems"
                    :key="item[identityKey]"
                    :id="`sel-${item[identityKey]}`"
                    role="option"
                    class="list-item"
                    :aria-selected="selectedActiveIndex === index"
                    @click="setSelectedActive(index)"
                >
                    <slot
                        name="selected-item"
                        :item="item"
                        :index="index"
                        :displayKey="displayKey"
                        :identityKey="identityKey"
                    >
                        <div class="item-label">{{ item[displayKey] }}</div>
                    </slot>

                    <div class="item-controls">
                        <button
                            v-if="sortable"
                            @click="moveItemUp(index)"
                            :disabled="index === 0"
                            class="control-btn"
                            :aria-label="$gettext('Nach oben verschieben')"
                        >
                            <studip-icon shape="arr_1up" />
                        </button>

                        <button
                            v-if="sortable"
                            @click="moveItemDown(index)"
                            :disabled="index === selectedItems.length - 1"
                            class="control-btn"
                            :aria-label="$gettext('Nach unten verschieben')"
                        >
                            <studip-icon shape="arr_1down" />
                        </button>

                        <button
                            v-if="removable"
                            @click="
                                setSelectedActive(index);
                                removeItem(item[identityKey]);
                            "
                            class="control-btn remove-btn"
                            :aria-label="`${$gettext('Entfernen')}: ${item[displayKey]}`"
                        >
                            <studip-icon shape="decline" />
                        </button>
                    </div>
                </li>

                <li v-if="selectedItems.length === 0" class="no-results">
                    {{ emptySelectedHint }}
                </li>
            </ul>
        </section>
    </div>
</template>

<script setup>
import { ref, computed, nextTick } from 'vue';
import StudipIcon from '@/vue/components/StudipIcon.vue';
import { $gettext } from '@/assets/javascripts/lib/gettext';

/* PROPS */
const props = defineProps({
    modelValue: { type: Array, default: () => [] },
    availableItems: { type: Array, default: () => [] },
    availableTitle: { type: String, default: $gettext('Verfügbare Elemente') },
    emptyAvailableHint: { type: String, default: $gettext('Keine weiteren Elemente gefunden.') },
    selectedTitle: { type: String, default: $gettext('Ausgewählte Elemente') },
    emptySelectedHint: { type: String, default: $gettext('Wählen Sie Elemente aus der linken Liste aus.') },
    labelKey: { type: String, default: 'name' },
    idKey: { type: String, default: 'id' },
    removable: { type: Boolean, default: true },
    sortable: { type: Boolean, default: true },
});

const emit = defineEmits(['update:modelValue']);
const searchTerm = ref('');
const isKeyboardMode = ref(false);

/* KEYS */
const displayKey = computed(() => props.labelKey);
const identityKey = computed(() => props.idKey);

/* SELECTED IDS */
const selectedIds = computed({
    get: () => (Array.isArray(props.modelValue) ? props.modelValue : []),
    set: (value) => emit('update:modelValue', value),
});

/* SELECTED ITEMS */
const selectedItems = computed(() => {
    const map = new Map(props.availableItems.map((i) => [i[identityKey.value], i]));
    return selectedIds.value.map((id) => map.get(id)).filter(Boolean);
});

/* AVAILABLE FILTERED */
const availableFiltered = computed(() => {
    const selectedSet = new Set(selectedIds.value);
    let list = props.availableItems.filter((item) => !selectedSet.has(item[identityKey.value]));

    if (searchTerm.value) {
        const s = searchTerm.value.toLowerCase();
        list = list.filter((item) => (item[displayKey.value] || '').toLowerCase().includes(s));
    }
    return list;
});

/* ACTIVE INDEX STATE */
const availableActiveIndex = ref(-1);
const selectedActiveIndex = ref(-1);

/* SAFE ACTIVE IDS */
const activeAvailableId = computed(() => {
    if (
        availableFiltered.value.length > 0 &&
        availableActiveIndex.value >= 0 &&
        availableActiveIndex.value < availableFiltered.value.length
    ) {
        return `avail-${availableFiltered.value[availableActiveIndex.value][identityKey.value]}`;
    }
    return null;
});

const activeSelectedId = computed(() => {
    if (
        selectedItems.value.length > 0 &&
        selectedActiveIndex.value >= 0 &&
        selectedActiveIndex.value < selectedItems.value.length
    ) {
        return `sel-${selectedItems.value[selectedActiveIndex.value][identityKey.value]}`;
    }
    return null;
});

/* FOCUS */
function focusAvailableList() {
    document.querySelector('.available-list .item-list')?.focus();
}
function focusSelectedList() {
    document.querySelector('.selected-list .item-list')?.focus();
}
function handleAvailableFocus() {
    if (isKeyboardMode.value && availableActiveIndex.value === -1 && availableFiltered.value.length > 0) {
        availableActiveIndex.value = 0;
    }
}
function handleSelectedFocus() {
    if (isKeyboardMode.value && selectedActiveIndex.value === -1 && selectedItems.value.length > 0) {
        selectedActiveIndex.value = 0;
    }
}
function handleAvailableMouseMove() {
    if (!isKeyboardMode.value && availableActiveIndex.value !== -1) {
        availableActiveIndex.value = -1;
    }
}
function handleSelectedMouseMove() {
    if (!isKeyboardMode.value && selectedActiveIndex.value !== -1) {
        selectedActiveIndex.value = -1;
    }
}

/* ADD / REMOVE */
function addItem(item) {
    const id = item[identityKey.value];
    if (!selectedIds.value.includes(id)) {
        selectedIds.value = [...selectedIds.value, id];
        nextTick(() => {
            if (isKeyboardMode.value) {
                selectedActiveIndex.value = selectedItems.value.length - 1;
            } else {
                selectedActiveIndex.value = -1;
            }
        });
    }
}

function removeItem(id) {
    if (!props.removable) return;
    const index = selectedIds.value.indexOf(id);
    selectedIds.value = selectedIds.value.filter((x) => x !== id);
    nextTick(() => {
        selectedActiveIndex.value = Math.min(index, selectedItems.value.length - 1);
    });
}

/* SORTING */
function moveItemUp(index) {
    if (!props.sortable || index === 0) return;
    const arr = [...selectedIds.value];
    [arr[index - 1], arr[index]] = [arr[index], arr[index - 1]];
    selectedIds.value = arr;
}

function moveItemDown(index) {
    if (!props.sortable || index === selectedIds.value.length - 1) return;
    const arr = [...selectedIds.value];
    [arr[index], arr[index + 1]] = [arr[index + 1], arr[index]];
    selectedIds.value = arr;
}

/* KEYBOARD: AVAILABLE */
window.addEventListener('keydown', () => {
    isKeyboardMode.value = true;
});

window.addEventListener('mousedown', () => {
    isKeyboardMode.value = false;
});
function handleAvailableListKeydown(event) {
    if (!availableFiltered.value.length) return;

    switch (event.key) {
        case 'ArrowDown':
            event.preventDefault();
            availableActiveIndex.value = Math.min(availableActiveIndex.value + 1, availableFiltered.value.length - 1);
            nextTick(() => {
                const id = activeAvailableId.value;
                if (id) {
                    document.getElementById(id)?.scrollIntoView({ block: 'nearest' });
                }
            });
            break;

        case 'ArrowUp':
            event.preventDefault();
            availableActiveIndex.value = Math.max(0, availableActiveIndex.value - 1);
            nextTick(() => {
                const id = activeAvailableId.value;
                if (id) {
                    document.getElementById(id)?.scrollIntoView({ block: 'nearest' });
                }
            });
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

/* KEYBOARD: SELECTED */
function handleSelectedListKeydown(event) {
    if (!selectedItems.value.length) return;

    switch (event.key) {
        case 'ArrowDown':
            event.preventDefault();
            if (event.shiftKey && props.sortable) {
                moveItemDown(selectedActiveIndex.value);
            } else {
                selectedActiveIndex.value = Math.min(selectedActiveIndex.value + 1, selectedItems.value.length - 1);
            }

            nextTick(() => {
                const id = activeSelectedId.value;
                if (id) {
                    document.getElementById(id)?.scrollIntoView({ block: 'nearest' });
                }
            });
            break;

        case 'ArrowUp':
            event.preventDefault();
            if (event.shiftKey && props.sortable) {
                moveItemUp(selectedActiveIndex.value);
            } else {
                selectedActiveIndex.value = Math.max(0, selectedActiveIndex.value - 1);
            }

            nextTick(() => {
                const id = activeSelectedId.value;
                if (id) {
                    document.getElementById(id)?.scrollIntoView({ block: 'nearest' });
                }
            });
            break;

        case 'Delete':
        case 'Backspace': {
            event.preventDefault();
            const item = selectedItems.value[selectedActiveIndex.value];
            if (item) removeItem(item[identityKey.value]);
            break;
        }

        case 'ArrowLeft':
            event.preventDefault();
            focusAvailableList();
            break;
    }
}

function setAvailableActive(i) {
    availableActiveIndex.value = i;
}
function setSelectedActive(i) {
    selectedActiveIndex.value = i;
}
</script>

<style scoped>
.studip-dual-list-box {
    display: flex;
    gap: 20px;
    margin-top: 15px;
}
.studip-dual-list-box ::v-deep(.studip-icon) {
    color: var(--color--highlight);
}
.list-column {
    position: relative;
    flex: 1;
    border: 1px solid var(--studip-border-color, #ccc);
    padding: 0 10px 10px 10px;
    height: 350px;
    overflow-y: auto;
    background-color: #fff;
}
.list-header {
    position: sticky;
    top: 0;
    z-index: 10;
    background: #fff;
    padding: 8px;
    margin-top: 0;
    margin-bottom: 10px;
    border-bottom: solid thin #ccc;
}
.list-search-input {
    width: 100%;
    padding: 8px;
    margin-bottom: 10px;
    box-sizing: border-box;
    border: 1px solid #ccc;
    border-radius: 4px;
}
.item-list {
    list-style: none;
    padding: 0;
    margin: 0;
}
.list-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 8px;
    margin-bottom: 4px;
    background-color: var(--studip-bg-default, #fff);
    border: 1px solid transparent;
    border-radius: 3px;
    transition: background-color 0.2s, border-color 0.2s;
    cursor: pointer;
    border-bottom-color: #ededed;
}
.list-item:last-child {
    border-bottom-color: transparent;
}
.list-item:hover,
.list-item:focus {
    background-color: var(--studip-bg-highlight, #e6f7ff);
    border-color: var(--studip-border-color-accent, #007bff);
    outline: none; /* Fokus-Stil des Browsers überschreiben */
}
.list-item.available {
    justify-content: space-between;
}
.list-item.selected .item-label {
    flex-grow: 1;
    padding-right: 10px;
}
.item-controls {
    display: flex;
    align-items: center;
}
.control-btn {
    background: none;
    border: none;
    cursor: pointer;
    font-size: 1em;
    padding: 0 4px;
}
.control-btn:disabled {
    opacity: 0.3;
    cursor: not-allowed;
}
.no-results {
    text-align: center;
    color: #888;
    padding: 20px 0;
}
.list-item[aria-selected='true'] {
    background-color: var(--studip-bg-highlight, #e6f7ff);
    border-color: var(--studip-border-color-accent, #007bff);
}
</style>
