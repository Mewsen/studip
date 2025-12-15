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

const emit = defineEmits(['update-config', 'close']);

function saveConfig() {
    emit('update-config');
}

function handleClose() {
    emit('close');
}
</script>
