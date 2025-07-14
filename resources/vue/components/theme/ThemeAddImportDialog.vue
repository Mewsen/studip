<template>
    <StudipDialog
        v-if="displayThemeAddImportDialog"
        :title="$gettext('Theme importieren')"
        :closeText="$gettext('Schließen')"
        :confirmText="$gettext('Importieren')"
        closeClass="cancel"
        :confirmDisabled="!hasErrors"
        confirmClass="upload"
        height="500"
        width="400"
        @close="closeDialog"
        @confirm="createThemeFromFile"
    >
        <template v-slot:dialogContent>
            <StudipMessageBox v-if="fileError" :type="'error'" :hideClose="true">
                {{ fileError }}
            </StudipMessageBox>
            <div class="theme-upload-actions">
                <input type="file" ref="fileInput" @change="handleFileUpload" accept=".json" class="visually-hidden" />
                <div class="file-dropzone-wrapper" @click="triggerFileInput">
                    <div class="file-dropzone" :class="{ 'has-file': fileSelected }">
                        <StudipIcon :shape="fileName ? 'file' : 'upload'" :size="48" />
                        <span>{{
                            fileName ? fileName : $gettext('Theme-Datei auswählen oder via Drag and Drop hinzufügen')
                        }}</span>
                    </div>
                </div>
            </div>
        </template>
    </StudipDialog>
</template>

<script setup>
import { computed, getCurrentInstance, ref } from 'vue';
import StudipDialog from '../StudipDialog.vue';
import StudipMessageBox from '../StudipMessageBox.vue';
import { useStore } from 'vuex';

const { proxy } = getCurrentInstance()
const store = useStore();
const displayThemeAddImportDialog = computed(() => store.getters['theme-settings-module/showThemeAddImportDialog']);
const hasErrors = computed(() => !fileError.value && fileSelected.value && jsonData.value);

const showThemeAddImportDialog = (show) => {
    store.dispatch('theme-settings-module/setShowThemeAddImportDialog', show);
};
const fileInput = ref(null);
const fileSelected = ref(false);
const fileName = ref('');
const jsonData = ref(null);
const fileError = ref(null);

const triggerFileInput = () => {
    fileInput.value?.click();
};

const isValidThemeData = (data) => {
    if (typeof data !== 'object' || data === null) return false;
    if (typeof data.name !== 'string' || data.name.trim() === '') return false;
    if (data.description && typeof data.description !== 'string') return false;
    if (data.type && !['light', 'dark', 'high-contrast'].includes(data.type)) return false;
    if (typeof data.values !== 'object' || data.values === null || Array.isArray(data.values)) return false;
    return true;
};
const handleFileUpload = (event) => {
    fileSelected.value = false;
    jsonData.value = null;
    fileError.value = null;
    fileName.value = '';

    const file = event.target.files[0];
    if (file && file.type === 'application/json') {
        fileName.value = file.name;
        const reader = new FileReader();
        reader.onload = () => {
            try {
                const parsed = JSON.parse(reader.result);
                if (isValidThemeData(parsed)) {
                    jsonData.value = parsed;
                    fileSelected.value = true;
                } else {
                    fileError.value = proxy.$gettext('Die JSON-Datei enthält keine gültigen Themendaten.');
                }
            } catch {
                fileError.value = proxy.$gettext('Ungültiges JSON-Format.');
            }
        };
        reader.readAsText(file);
    } else {
        fileError.value = proxy.$gettext('Bitte laden Sie eine gültige .json-Datei hoch.');
    }
};
const createThemeFromFile = async () => {
    if (jsonData.value) {
        const newTheme = {
            attributes: {
                name: jsonData.value.name,
                description: jsonData.value.description || '',
                type: jsonData.value.type || 'light',
                values: jsonData.value.values,
            },
        };

        await store.dispatch('theme-settings-module/createThemeFromData', { theme: newTheme });
        fileSelected.value = false;
        jsonData.value = null;
        fileName.value = '';
        showThemeAddImportDialog(false);
    }
};

const closeDialog = () => {
    removeFile();
    showThemeAddImportDialog(false);
};

const removeFile = () => {
    fileInput.value.value = '';
    fileSelected.value = false;
    fileName.value = '';
    jsonData.value = null;
    fileError.value = null;
};
</script>
