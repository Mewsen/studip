<template>
    <div class="theme-settings-app-wrapper">
        <ThemeEditor
            v-if="selectedTheme"
            :theme="selectedTheme"
            @back="clearSelection"
        />

        <ThemeList
            v-else
            :themes="themes"
            @select="selectTheme"
            @activate="activateTheme"
            @add="addTheme"
        />
    </div>
</template>

<script setup>
import { computed, ref } from 'vue';
import { useStore } from 'vuex';
import ThemeList from '@/vue/components/theme/ThemeList.vue';
import ThemeEditor from '@/vue/components/theme/ThemeEditor.vue';


const store = useStore();
const themes = computed(() => store.getters['studip-themes/all']);
const selectedTheme = ref(null);

const selectTheme = (theme) => {
    selectedTheme.value = theme;
};

const activateTheme = (theme) => {
    store.dispatch('theme-settings-module/activateTheme', {theme: theme}).then(() => {
        window.location.reload();
    });
};

const addTheme = () => {
    store.dispatch('theme-settings-module/addTheme').then((theme) => {
        selectTheme(theme);
    });
};

const clearSelection = () => {
    selectedTheme.value = null;
};

store.dispatch('users/loadById', { id: store.getters['theme-settings-module/userId'] });
store.dispatch('studip-themes/loadAll', {options: {}});
</script>
