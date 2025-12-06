<template>
    <StudipDialog
        :title="$gettext('Einstellungen bearbeiten')"
        :width="700"
        :height="700"
        :confirm-show="true"
        :confirm-text="$gettext('Speichern')"
        confirm-class="accept"
        :close-text="$gettext('Abbrechen')"
        @close="handleClose"
        @confirm="saveConfig"
    >
        <template #dialogContent>
            <slot></slot>
        </template>
        
        </StudipDialog>
</template>

<script setup>
import StudipDialog from '@/vue/components/StudipDialog.vue';
import { defineProps, defineEmits } from 'vue';

const props = defineProps({
    currentConfig: Object, 
    widgetId: [String, Number],
});

const emit = defineEmits(['update-config', 'close']);

function saveConfig() {
    const configToSend = JSON.parse(JSON.stringify(props.currentConfig));
    
    emit('update-config', configToSend);
}

function handleClose() {
    emit('close');
}
</script>
