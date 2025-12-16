import { ref } from 'vue';
import { defineStore } from 'pinia';
import { api } from '../kitsu-api.js';

export const useWidgetMiscStore = defineStore('widgetMiscStore', () => {
    const widgetTypes = ref(null);
    const breakpoints = ref(null);
    const breakpointsWidth = ref(null);
    const breakpointsCols = ref(null);
    const editMode = ref(false);

    function setEditMode(state) {
            editMode.value = state;
    }

    async function fetchMisc() {
        try {
            const response = await api.get('dashboard-widgets-misc');
            widgetTypes.value = response['widget-types'];
            breakpoints.value = response['breakpoints'];
            breakpointsWidth.value = response['breakpoints-widths'];
            breakpointsCols.value = response['breakpoints-cols'];
        } catch (error) {
            console.error('Fehler beim Abrufen der Widget-Misc-Daten:', error);
        }
    }

    return {
        editMode,
        widgetTypes,
        breakpoints,
        breakpointsWidth,
        breakpointsCols,

        setEditMode,
        fetchMisc,
    };
});
