<template>
    <StudipDialog
        v-if="displayThemeAddDialog"
        :title="$gettext('Theme hinzufügen')"
        :closeText="$gettext('Schließen')"
        closeClass="cancel"
        height="320"
        width="680"
        @close="showThemeAddDialog(false)"
    >
        <template v-slot:dialogContent>
            <div class="square-button-panel">
                <StudipSquareButton
                    icon="add"
                    :title="$gettext('Neu erstellen')"
                    @click="showThemeAddNewDialog(true)"
                />
                <StudipSquareButton
                    icon="import"
                    :title="$gettext('Aus Datei importieren')"
                    @click="showThemeAddImportDialog(true)"
                />
                <StudipSquareButton
                    icon="copy"
                    :title="$gettext('Duplizieren')"
                    @click="showThemeAddCopyDialog(true)"
                />
            </div>
        </template>
    </StudipDialog>
</template>

<script setup>
import { computed } from 'vue';
import StudipDialog from '../StudipDialog.vue';
import StudipSquareButton from '../StudipSquareButton.vue';
import { useStore } from 'vuex';

const emit = defineEmits(['add']);

const store = useStore();

const displayThemeAddDialog = computed(() => store.getters['theme-settings-module/showThemeAddDialog']);

const showThemeAddDialog = (show) => {
    store.dispatch('theme-settings-module/setShowThemeAddDialog', show);
};
const showThemeAddNewDialog = () => {
    store.dispatch('theme-settings-module/setShowThemeAddDialog', false);
    emit('add');
};
const showThemeAddImportDialog = () => {
    store.dispatch('theme-settings-module/setShowThemeAddDialog', false);
    store.dispatch('theme-settings-module/setShowThemeAddImportDialog', true);
};
const showThemeAddCopyDialog = () => {
    store.dispatch('theme-settings-module/setShowThemeAddDialog', false);
    store.dispatch('theme-settings-module/setShowThemeAddCopyDialog', true);
};
</script>
