<template>
    <div class="theme-list">
        <h2>{{ $gettext('Farbeinstellungen') }}</h2>
        <div class="theme-categories">
            <div class="theme-category">
                <ul class="theme-list">
                    <li
                        v-for="theme in filteredThemes('light')"
                        :key="theme.id"
                        :class="{ active: theme.attributes.active }"
                    >
                        <div class="theme-info">
                            <div class="theme-meta">
                                <strong class="theme-name">{{ theme.attributes.name }}</strong>
                                <p class="theme-description">{{ theme.attributes.description }}</p>
                            </div>
                            <div class="theme-colors">
                                <div
                                    v-for="color in colorKeys"
                                    :key="color"
                                    class="theme-color"
                                    :title="theme.attributes.values?.[color]"
                                    :style="{ backgroundColor: theme.attributes.values?.[color] || 'transparent' }"
                                />
                            </div>
                        </div>
                        <div class="theme-actions">
                            <button class="button" @click="$emit('select', theme)">
                                {{ $gettext('Bearbeiten') }}
                            </button>
                            <button
                                v-if="!theme.attributes.active"
                                class="button"
                                @click="$emit('activate', theme)"
                            >
                                {{ $gettext('Aktivieren') }}
                            </button>
                        </div>
                    </li>
                    <li class="theme-add">
                        <button @click="showThemeAddDialog(true)">
                            <StudipIcon shape="add" :size="24" />
                            {{ $gettext('Neues Theme hinzufügen') }}
                        </button>
                    </li>
                </ul>
            </div>
        </div>
        <ThemeAddDialog  @add="$emit('add')"/>
        <ThemeAddImportDialog />
        <ThemeAddCopyDialog :themes="filteredThemes('light')" />
    </div>
</template>

<script setup>
import StudipIcon from '../StudipIcon.vue';
import ThemeAddDialog from './ThemeAddDialog.vue';
import ThemeAddImportDialog from './ThemeAddImportDialog.vue';
import ThemeAddCopyDialog from './ThemeAddCopyDialog.vue';
import { useStore } from 'vuex';

const props = defineProps({
    themes: {
        type: Array,
        required: true,
    },
});

defineEmits(['add', 'select', 'activate']);

const store = useStore();

const colorKeys = [
    '--color--brand-primary',
    '--color--main-navigation-item',
    '--color--sidebar-item',
    '--color--highlight',
    '--color--content-link',
];

const filteredThemes = (type) => {
    return props.themes
        .filter((theme) => theme.attributes.type === type)
        .sort((a, b) => Number(b.attributes.active) - Number(a.attributes.active));
};

const showThemeAddDialog = (show) => {
    store.dispatch('theme-settings-module/setShowThemeAddDialog', show);
};

</script>
