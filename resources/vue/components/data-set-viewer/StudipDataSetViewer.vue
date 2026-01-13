<template>
    <div class="data-set-viewer">
        <header class="viewer-header">
            <div class="header-left">
                <slot name="header-left">
                    </slot>
            </div>

            <div class="header-center">
                <slot name="header-center">
                    </slot>
            </div>

            <div class="header-right">
                <slot name="header-right"></slot>
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
            <component :is="activeViewComponent" :data="data" v-bind="commonProps">
                <template v-for="(_, name) in $slots" #[name]="slotProps">
                    <slot :name="name" v-bind="slotProps" />
                </template>
            </component>
        </div>
    </div>
</template>

<script setup>
import { computed, getCurrentInstance, ref } from 'vue';
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
        default: () => ({})
    }
});

const { proxy } = getCurrentInstance();

const activeView = ref(props.availableViews[0] || 'table');

const getIconShape = (viewType) => {
    const shapeMap = {
        table: 'billboard', // TODO: we need a view-table icon
        card: 'view-wall',
        list: 'view-list'
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
        list: proxy.$gettext('Listenansicht') 
    };
    return mapping[name] || name;
};

const activeViewComponent = computed(() => {
    if (props.viewComponents[activeView.value]) {
        return props.viewComponents[activeView.value];
    }
    const map = { table: TableView, card: CardView, list: ListView };
    return map[activeView.value];
});

const commonProps = computed(() => ({
    titleKey: props.titleKey
}));
</script>
<style scoped>
.viewer-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 0;
    margin-bottom: 20px;
    border-bottom: 2px solid #eee;
    gap: 15px;
}

.header-left, .header-right {
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
</style>