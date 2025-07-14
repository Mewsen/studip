<template>
    <StudipDialog
        v-if="displayThemeAddCopyDialog"
        :title="$gettext('Theme duplizieren')"
        :closeText="$gettext('Schließen')"
        closeClass="cancel"
        :confirmText="$gettext('Duplizieren')"
        confirmClass="accept"
        height="240"
        width="400"
        @close="closeDialog"
        @confirm="copyTheme"
    >
        <template #dialogContent>
            <form class="default">
                <label>
                    {{ $gettext('Theme') }}
                <StudipSelect
                    v-model="theme"
                    :options="props.themes"
                    :label="$gettext('Theme auswählen')"
                    :clearable="false"
                >
                    <template #open-indicator="{ selectAttributes }">
                        <span v-bind="selectAttributes"><studip-icon shape="arr_1down" :size="10" /></span>
                    </template>
                    <template #no-options>
                        {{ $gettext('Es steht keine Auswahl zur Verfügung.') }}
                    </template>
                    <template #selected-option="option">
                        <span>{{ option.attributes.name }}</span>
                    </template>
                    <template #option="option">
                        <span>{{ option.attributes.name }}</span>
                    </template>
                </StudipSelect>
                </label>
            </form>
        </template>
    </StudipDialog>
</template>
<script setup>
import {computed, getCurrentInstance, ref} from 'vue';
import StudipDialog from '../StudipDialog.vue';
import StudipSelect from '../StudipSelect.vue';
import { useStore } from 'vuex';

const { proxy } = getCurrentInstance();

const store = useStore();
const props = defineProps({
    themes: {
        type: Array,
        required: true,
    },
});
const theme = ref('');

const displayThemeAddCopyDialog = computed(() => store.getters['theme-settings-module/showThemeAddCopyDialog']);
const showThemeAddCopyDialog = (show) => {
    store.dispatch('theme-settings-module/setShowThemeAddCopyDialog', show);
};
const closeDialog = () => {
    showThemeAddCopyDialog(false);
};

const copyTheme = async () => {
    const newTheme = theme.value;
    newTheme.attributes.name = `${newTheme.attributes.name} (${proxy.$gettext('Kopie')})`;
    await store.dispatch('theme-settings-module/createThemeFromData', { theme: newTheme });
    closeDialog();
};
</script>
