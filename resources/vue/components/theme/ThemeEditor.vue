<template>
    <section class="theme-editor">
        <ContentBar isContentBar icon="colorpicker">
            <template #breadcrumb-list>
                {{ editableName }}
            </template>
            <template v-if="hasChanges && !isDisabled" #buttons-right>
                <button class="button accept" @click="storeChanges">
                    {{ $gettext('Speichern') }}
                </button>
                <button class="button cancel" @click="resetColors">
                    {{ $gettext('Zurücksetzen') }}
                </button>
            </template>
            <template #info-text>
                {{ editableDescription }}
            </template>
            <template #menu>
                <StudipActionMenu
                    :items="menuItems"
                    context="Theme Editor"
                    :collapseAt="1"
                    @deleteTheme="displayDeleteDialog"
                    @exportTheme="exportTheme"
                />
            </template>
        </ContentBar>
        <div class="theme-editor-header">
            <form class="default collapsable">
                <fieldset class="collapsed">
                    <legend>{{ $gettext('Metadaten') }}</legend>

                    <label>
                        {{ $gettext('Name') }}
                        <input type="text" v-model="editableName" :disabled="isDisabled" />
                    </label>

                    <label>
                        {{ $gettext('Beschreibung') }}
                        <input type="text" v-model="editableDescription" :disabled="isDisabled" />
                    </label>
                </fieldset>
            </form>
        </div>

        <div class="color-group" v-for="(group, groupName) in editableColors" :key="groupName">
            <h3 class="group-title">{{ getReadableGroupName(groupName) }}</h3>
            <div class="color-grid">
                <div v-for="(hex, name) in group" :key="name" class="color-entry">
                    <div class="color-entry-color" :style="{ backgroundColor: editableColors[groupName][name] }"></div>
                    <div class="color-entry-content">
                        <div class="color-entry-text">
                            <p class="color-entry-name">{{ name }}</p>
                            <p class="color-entry-hex">{{ editableColors[groupName][name] }}</p>
                        </div>
                        <div class="color-entry-buttons">
                            <ColorPicker v-model="editableColors[groupName][name]" :disabled="isDisabled" />
                            <button
                                class="button btn-icon--only"
                                @click="deleteColor(groupName, name)"
                                :disabled="isDisabled"
                            >
                                <StudipIcon shape="trash" />
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <form class="default" v-if="Object.keys(editableColors).length > 0 && !isDisabled" @submit.prevent>
            <fieldset class="color-entry new-color-entry collapsable collapsed">
                <legend>{{ $gettext('Benutzerdefinierte Farbe') }}</legend>
                <label>
                    {{ $gettext('Farbgruppe') }}
                    <input type="text" v-model="newCustomKey" />
                </label>
                <label>
                    {{ $gettext('Farbwert') }}
                    <ColorPicker v-model="newCustomValue" :with-color="true" />
                </label>
                <button
                    class="button add"
                    @click.prevent="addCustomColor"
                    :disabled="!newCustomKey || !newCustomValue || keyExists"
                >
                    {{ $gettext('Hinzufügen') }}
                </button>
                <small v-if="keyExists" style="color: red">{{ $gettext('Dieser Key existiert bereits.') }}</small>
            </fieldset>
        </form>
    </section>
    <StudipDialog
        v-if="showDeleteDialog"
        :title="$gettext('Theme löschen')"
        :question="
            $gettext(
                'Möchten Sie das Theme %{ ThemeTitle } wirklich löschen?',
                { ThemeTitle: props.theme.attributes.name },
                true,
            )
        "
        height="200"
        @confirm="executeDelete"
        @close="showDeleteDialog = false"
    />
    <StudipModalLeave :has-unsaved-changes="hasChanges" :on-save="storeChanges" />
</template>

<script setup>
import { computed, getCurrentInstance, ref, watch } from 'vue';
import { useStore } from 'vuex';
import ColorPicker from '../colorPicker/ColorPicker.vue';
import StudipIcon from '../StudipIcon.vue';
import ContentBar from '../ContentBar.vue';
import StudipActionMenu from '../StudipActionMenu.vue';
import StudipDialog from '../StudipDialog.vue';
import StudipModalLeave from '../StudipModalLeave.vue';

const props = defineProps({
    theme: {
        type: Object,
        required: true,
    },
});
const emit = defineEmits(['back']);
const store = useStore();
const { proxy } = getCurrentInstance();

const editableColors = ref({});
const originalColors = ref({});
const editableName = ref('');
const originalName = ref('');

const editableType = ref('');
const originalType = ref('');

const editableDescription = ref('');
const originalDescription = ref('');

const showDeleteDialog = ref(false);

const menuItems = [
    {
        id: 1,
        label: proxy.$gettext('Exportieren'),
        icon: 'export',
        emit: 'exportTheme',
    },
    {
        id: 2,
        label: proxy.$gettext('Löschen'),
        icon: 'trash',
        emit: 'deleteTheme',
    },
];

const isDisabled = computed(() => props.theme.attributes.origin === 'system');

const hasChanges = computed(() => {
    if (isDisabled.value) return false;

    return (
        JSON.stringify(editableColors.value) !== originalColors.value ||
        editableName.value !== originalName.value ||
        editableType.value !== originalType.value ||
        editableDescription.value !== originalDescription.value
    );
});

const newCustomKey = ref('');
const newCustomValue = ref('#000000');
const keyExists = computed(() => {
    const key = newCustomKey.value.trim();
    if (!key) return false;

    return Object.values(editableColors.value).some((group) => key in group);
});

const addCustomColor = () => {
    const key = newCustomKey.value.trim();
    const value = newCustomValue.value;

    if (!key || !value || keyExists.value) return;

    const categories = props.theme.meta.colorKeyCategories || {};
    const targetGroup =
        Object.entries(categories).find(([, keys]) => {
            return Array.isArray(keys) ? keys.includes(key) : key in keys;
        })?.[0] ?? 'custom';

    if (!editableColors.value[targetGroup]) {
        editableColors.value[targetGroup] = {};
    }

    editableColors.value[targetGroup][key] = value;

    newCustomKey.value = '';
    newCustomValue.value = '#000000';
};

const deleteColor = (groupName, key) => {
    if (isDisabled.value || !editableColors.value[groupName]) return;

    const updatedGroup = { ...editableColors.value[groupName] };
    delete updatedGroup[key];

    if (Object.keys(updatedGroup).length === 0) {
        const updatedColors = { ...editableColors.value };
        delete updatedColors[groupName];
        editableColors.value = updatedColors;
    } else {
        editableColors.value[groupName] = updatedGroup;
    }
};

const resetColors = () => {
    const themeColors = props.theme.attributes.values || {};
    const categories = props.theme.meta.colorKeyCategories || {};
    const newEditableColors = {};

    const systemKeys = new Set();

    Object.entries(categories).forEach(([groupName, keys]) => {
        newEditableColors[groupName] = {};

        const keyList = Array.isArray(keys) ? keys : Object.keys(keys);

        keyList.forEach((key) => {
            systemKeys.add(key);
            newEditableColors[groupName][key] = themeColors[key] || (Array.isArray(keys) ? '#FFFFFF' : keys[key]);
        });
    });

    Object.entries(themeColors).forEach(([key, value]) => {
        if (!systemKeys.has(key)) {
            if (!newEditableColors['custom']) {
                newEditableColors['custom'] = {};
            }
            newEditableColors['custom'][key] = value;
        }
    });

    editableColors.value = newEditableColors;

    originalColors.value = JSON.stringify(newEditableColors);

    editableName.value = props.theme.attributes.name;
    originalName.value = props.theme.attributes.name;
    editableDescription.value = props.theme.attributes.description || '';
    originalDescription.value = props.theme.attributes.description || '';
};

const storeChanges = async () => {
    const updatedColors = Object.values(editableColors.value).reduce((acc, group) => {
        Object.assign(acc, group);
        return acc;
    }, {});

    const updatedTheme = {
        ...props.theme,
        attributes: {
            ...props.theme.attributes,
            values: updatedColors,
            name: editableName.value,
            // type: editableType.value,
            type: 'light', // Temporarily set to 'light' until dark mode is implemented
            description: editableDescription.value,
        },
    };

    await store.dispatch('theme-settings-module/updateTheme', { theme: updatedTheme });
    resetColors();
};

const displayDeleteDialog = () => {
    showDeleteDialog.value = true;
};
const executeDelete = () => {
    if (isDisabled.value) return;
    const id = props.theme.id;
    store.dispatch('theme-settings-module/deleteTheme', { id }).then(() => {
        emit('back');
    });
};

const exportTheme = () => {
    const themeData = {
        name: props.theme.attributes.name,
        description: props.theme.attributes.description || '',
        author: props.theme.attributes.author || '',
        version: props.theme.attributes.version || '1.0',
        type: props.theme.attributes.type || 'light',
        values: props.theme.attributes.values || {},
        studip_min_version: props.theme.attributes.studip_min_version || '6.1',
        studip_max_version: props.theme.attributes.studip_max_version || '7.0',
    };

    const blob = new Blob([JSON.stringify(themeData, null, 2)], { type: 'application/json' });
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = `${props.theme.attributes.name || 'theme'}.json`;
    link.click();
};

const getReadableGroupName = (groupName) => {
    const groupNames = {
        brand: proxy.$gettext('Basisfarbe'),
        general: proxy.$gettext('Allgemein'),
        text: proxy.$gettext('Text'),
        navigation: proxy.$gettext('Navigation'),
        sidebar: proxy.$gettext('Seitenleiste'),
        content: proxy.$gettext('Inhalt'),
        custom: proxy.$gettext('Benutzerdefiniert'),
    };
    return groupNames[groupName] || groupName.charAt(0).toUpperCase() + groupName.slice(1);
};

watch(() => props.theme, resetColors, { immediate: true });
</script>
