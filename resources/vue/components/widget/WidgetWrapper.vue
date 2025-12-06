<template>
    <div class="widget-wrapper">
        <header class="widget-header">
            <h4>{{ title }}</h4>
            <div v-if="isEditing">
                <button @click="showSettingsModal = true">
                    <studip-icon shape="edit" />
                </button>
                <button @click="$emit('delete-widget', widgetId)">
                    <studip-icon shape="trash" />
                </button>
            </div>
        </header>

        <main class="widget-content">
            <slot name="content"></slot>
        </main>

        <widget-settings-modal
            v-if="showSettingsModal"
            :widget-id="widgetId"
            :current-config="config"
            @update-config="handleConfigUpdate"
            @close="showSettingsModal = false"
        >
            <slot name="settings"></slot>
        </widget-settings-modal>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import WidgetSettingsModal from '@/vue/components/widget/WidgetSettingsModal.vue';
import StudipIcon from '@/vue/components/StudipIcon.vue';

const props = defineProps({
    title: {
        type: String,
        required: true,
    },
    widgetId: {
        type: Number,
        required: true,
    },
    config: {
        type: Object,
        required: true,
    },
    isEditing: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['delete-widget', 'update-config']);

const showSettingsModal = ref(false);

function handleConfigUpdate(newConfig) {
    // emit('update-config', props.widgetId, newConfig);
    console.log('handle update');
    console.log('widget id: ' + props.widgetId);
    console.log(newConfig);
    showSettingsModal.value = false;
}
</script>
