import { ref, computed } from 'vue';
import { defineStore } from 'pinia';
import { api } from '../kitsu-api.js';

export const useWidgetStore = defineStore('widgetStore', () => {

    const widgets = ref([]);
    const isLoading = ref(false);

    const getAllWidgets = computed(() => widgets.value);

    async function fetchContainerWidgets(containerId) {
        isLoading.value = true;
        try {
            const response = await api.get(`dashboard-widget-containers/${containerId}/widgets`);
            widgets.value = response.data;
        } catch (error) {
            console.error(`Fehler beim Laden der Widgets für Container ${containerId}:`, error);
            throw error;
        } finally {
            isLoading.value = false;
        }
    }

    async function addWidget(containerId, widgetType, widgetScope, payload, position, breakpoint) {
        const newWidgetData = {
            'widget-type': widgetType,
            'widget-scope': widgetScope,
            payload: payload,
            breakpoint: breakpoint,
            position: position,
        };

        try {
            const response = await api.create('dashboard-widgets', newWidgetData, {
                baseURL: `dashboard-widget-containers/${containerId}/`,
            });

            widgets.value.push(response.data);
            return response.data;
        } catch (error) {
            console.error('Fehler beim Hinzufügen des Widgets:', error);
            throw error;
        }
    }

    async function updateWidgetPayload(widgetId, newPayload) {
        try {
            const response = await api.update('dashboard-widgets', {
                id: widgetId,
                attributes: { payload: newPayload },
            });

            const index = widgets.value.findIndex((w) => w.id === widgetId);
            if (index !== -1) {
                widgets.value[index] = response.data;
            }
        } catch (error) {
            console.error('Fehler beim Aktualisieren des Widget-Payloads:', error);
            throw error;
        }
    }

    async function updateWidgetPosition(containerId, widgetId, position, breakpoint) {
        try {
            await api.update(
                'dashboard-widgets',
                {
                    id: widgetId,
                    attributes: {
                        breakpoint: breakpoint,
                        position: position,
                    },
                },
                {
                    baseURL: `dashboard-widget-containers/${containerId}/widgets/`,
                }
            );
        } catch (error) {
            console.error('Fehler beim Speichern der Widget-Position:', error);
            throw error;
        }
    }

    async function deleteWidget(containerId, widgetId) {
        try {
            await api.delete('dashboard-widgets', widgetId, {
                baseURL: `dashboard-widget-containers/${containerId}/widgets/`,
            });

            widgets.value = widgets.value.filter((w) => w.id !== widgetId);
        } catch (error) {
            console.error('Fehler beim Löschen des Widgets:', error);
            throw error;
        }
    }

    return {
        widgets,
        isLoading,
        getAllWidgets,
        fetchContainerWidgets,
        addWidget,
        updateWidgetPayload,
        updateWidgetPosition,
        deleteWidget,
    };
});
