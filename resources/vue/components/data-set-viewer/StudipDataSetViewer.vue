<template>
    <div class="data-set-viewer">
        <header class="viewer-header">
            <div class="header-left">
                <slot name="header-left" v-bind="slotApi"> </slot>
            </div>

            <div class="header-center">
                <slot name="header-center" v-bind="slotApi"> </slot>
            </div>

            <div class="header-right">
                <slot name="header-right" v-bind="slotApi"></slot>
                <studip-button-group radiogroup v-if="availableViews.length > 1">
                    <button
                        v-for="viewType in availableViews"
                        :key="viewType"
                        class="button icon-only"
                        :class="{ active: activeView === viewType }"
                        :title="formatViewName(viewType)"
                        @click="activeView = viewType"
                    >
                        <studip-icon
                            :shape="getIconShape(viewType)"
                            :role="activeView === viewType ? 'info' : 'clickable'"
                            :size="16"
                        />
                    </button>
                </studip-button-group>
            </div>
        </header>
        <div class="data-view-content">
            <template v-if="data.length > 0">
                <component :is="activeViewComponent" :data="data" v-bind="commonProps">
                    <template v-for="(_, name) in $slots" #[name]="slotProps">
                        <slot :name="name" v-bind="slotProps" />
                    </template>
                </component>
            </template>
            <template v-else>
                <div class="data-set-empty-state">
                    <slot name="empty-state">
                        <div class="default-empty-message">
                            <studip-icon shape="info" :size="48" />
                            <p>{{ $gettext('Keine Einträge gefunden.') }}</p>
                        </div>
                    </slot>
                </div>
            </template>
        </div>
    </div>
</template>

<script setup>
import { computed, getCurrentInstance, provide, ref, watch } from 'vue';
import CardView from './CardView.vue';
import ListView from './ListView.vue';
import TableView from './TableView.vue';
import StudipButtonGroup from '../StudipButtonGroup.vue';

const props = defineProps({
    availableViews: {
        type: Array, // ['table', 'card', 'list']
        required: true,
        validator: (views) => views.every((v) => ['table', 'card', 'list'].includes(v)),
    },
    data: {
        type: Array,
        required: true,
    },
    titleKey: {
        type: String,
        required: false,
        default: 'title',
    },
    viewComponents: {
        type: Object,
        default: () => ({}),
    },
    selectionMode: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['update:selection-mode', 'selection-change']);

const { proxy } = getCurrentInstance();

const activeView = ref(props.availableViews[0] || 'table');

const getIconShape = (viewType) => {
    const shapeMap = {
        table: 'billboard', // TODO: we need a view-table icon
        card: 'view-wall',
        list: 'view-list',
    };
    return shapeMap[viewType] || 'view-list';
};

const viewComponentMap = {
    table: TableView,
    card: CardView,
    list: ListView,
};

const formatViewName = (name) => {
    const mapping = {
        table: proxy.$gettext('Tabellenansicht'),
        card: proxy.$gettext('Kacheln'),
        list: proxy.$gettext('Listenansicht'),
    };
    return mapping[name] || name;
};

const slotApi = computed(() => ({
    selectAll,
    deselectAll,
    countSelection: selectedIds.value.length,
    isSelectionMode: props.selectionMode,
}));

const activeViewComponent = computed(() => {
    if (props.viewComponents[activeView.value]) {
        return props.viewComponents[activeView.value];
    }
    const map = { table: TableView, card: CardView, list: ListView };
    return map[activeView.value];
});

const commonProps = computed(() => ({
    titleKey: props.titleKey,
}));

const selectedIds = ref([]);
const selectAll = () => {
    selectedIds.value = props.data.map((item) => item.id);
};

const deselectAll = () => {
    selectedIds.value = [];
};

provide('selectionContext', {
    isSelectionMode: computed(() => props.selectionMode),
    selectedIds: computed(() => selectedIds.value),
    toggleItem: (id) => {
        const index = selectedIds.value.indexOf(id);
        if (index > -1) selectedIds.value.splice(index, 1);
        else selectedIds.value.push(id);
    },
    selectAll,
    deselectAll,
});
watch(
    () => props.selectionMode,
    (newVal) => {
        if (!newVal) selectedIds.value = [];
    }
);
watch(
    selectedIds,
    (newSelection) => {
        emit('selection-change', [...newSelection]);
    },
    { deep: true }
);
</script>
<style scoped>
.viewer-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 0 20px 0;
    margin-bottom: 15px;
    border-bottom: 1px solid var(--color--divider);
    gap: 15px;
}

.header-left,
.header-right {
    display: flex;
    align-items: center;
    gap: 10px;
}

.header-center {
    flex: 1;
    display: flex;
    justify-content: center;
}

.header-center:empty {
    flex: 0;
}

.header-right {
    display: flex;
    align-items: center;
    gap: 16px;
}

.header-actions {
    display: flex;
    align-items: center;
    gap: 4px;
}

.search-wrapper input {
    width: 100%;
    min-width: 200px;
    padding: 4px 8px;
    border: 1px solid #ccc;
    border-radius: 4px;
}

.empty-state-container {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 60px 20px;
    text-align: center;
    color: var(--color--gray);

    .studip-icon {
        margin-bottom: 20px;
        opacity: 0.3;
    }

    h3 {
        margin: 0 0 10px 0;
        color: var(--color--base);
    }

    p {
        margin: 0 0 20px 0;
        max-width: 400px;
    }

    .empty-state-actions {
        display: flex;
        gap: 10px;
    }
}
</style>
