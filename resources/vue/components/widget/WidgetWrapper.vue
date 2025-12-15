<template>
    <div class="widget-wrapper">
        <header class="widget-header">
            <h4>{{ title }}</h4>
            <div v-if="isEditing">
                <button @click="showSettingsModal = true">
                    <studip-icon shape="edit" />
                </button>
                <button @click="deleteWidget">
                    <studip-icon shape="trash" />
                </button>
            </div>
        </header>

        <main class="widget-content">
            <slot name="content"></slot>
        </main>

        <widget-settings-modal
            v-if="showSettingsModal"
            @update-config="handleConfigUpdate"
            @close="showSettingsModal = false"
        >
            <slot name="settings"></slot>
        </widget-settings-modal>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { useContainerStore } from '@/vue/store/pinia/widget/dashboard-widget-containers.js';
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
    widgetData: {
        type: Object,
        required: true,
    },
    isEditing: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['delete-widget', 'update-config']);
const widgetContainerStore = useContainerStore();
const showSettingsModal = ref(false);

function handleConfigUpdate() {
    emit('update-config');
    showSettingsModal.value = false;
}

function deleteWidget() {
    emit('delete-widget', {
        'container-id': props.widgetData['container-id'],
        'widget-id': props.widgetId,
    });

    widgetContainerStore.deleteWidget(props.widgetData['container-id'], props.widgetId);
}
</script>
