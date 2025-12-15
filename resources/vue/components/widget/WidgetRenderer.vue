<template>
    <component
        v-if="widgetComponent && widget"
        :is="widgetComponent"
        :widget-id="widget.id"
        :widget-data="widget"
        :is-editing="isEditing"
        @update-config="emit('update-config', $event)"
        @delete-widget="emit('delete-widget', widget.id)"
    />
    <div v-else class="widget-error">
        Widget <strong>{{ widgetId }}</strong> konnte nicht geladen werden
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { useWidgetStore } from '@/vue/store/pinia/widget/dashboard-widgets.js';

const props = defineProps({
    widgetId: {
        type: String,
        required: true,
    },
    widgetComponents: {
        type: Object,
        required: true,
    },
    isEditing: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits([
    'update-config',
    'delete-widget',
]);

const widgetStore = useWidgetStore();
const widget = computed(() => widgetStore.byId(props.widgetId));
const widgetComponent = computed(() => {
    if (!widget.value) return null;

    const componentKey = widget.value['widget-component'];
    return props.widgetComponents[componentKey] || null;
});

</script>
