<template>
    <div class="data-set-viewer">
        <ViewSelector :views="availableViews" :active-view="activeView" @update:activeView="activeView = $event" />
        <div class="data-view-content">
            <component :is="activeViewComponent" :data="data" :title-key="props.titleKey">
                <template v-for="(_, name) in $slots" #[name]="slotProps">
                    <slot :name="name" v-bind="slotProps" />
                </template>
            </component>
        </div>
    </div>
</template>

<script setup>
import { computed, ref } from 'vue';
import ViewSelector from './ViewSelector.vue';
import CardView from './CardView.vue';
import ListView from './ListView.vue';
import TableView from './TableView.vue';

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
});
const activeView = ref(props.availableViews[0] || 'table');

const viewComponentMap = {
    table: TableView,
    card: CardView,
    list: ListView,
};

const activeViewComponent = computed(() => {
    return viewComponentMap[activeView.value];
});
</script>
