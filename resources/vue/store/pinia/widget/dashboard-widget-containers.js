import { ref, computed } from 'vue';
import { defineStore } from 'pinia';
import { api } from '../kitsu-api.js';
import { useWidgetStore } from './dashboard-widgets.js';

export const useContainerStore = defineStore('containerStore', () => {
    const widgetStore = useWidgetStore();
    const container = ref(null);
    const isLoading = ref(false);

    const containerId = computed(() => container.value?.id);

    const layouts = computed(() => container.value?.payload || {});

    const hasLayout = computed(() => {
        const layouts = container.value?.payload;
        if (!layouts || typeof layouts !== 'object') {
            return false;
        }
        for (const breakpointKey in layouts) {
            const layoutArray = layouts[breakpointKey];
            if (layoutArray.length > 0) {
                return true;
            }
        }
        return false;
    });

    function layoutForBreakpoint(breakpoint) {
        const layouts = container.value.payload;
        if (!layouts) {
            return {};
        }

        return layouts[breakpoint];
    }

    async function fetchOrCreateContainer(context, groupId) {
        isLoading.value = true;
        try {
            const response = await api.create(
                'dashboard-widget-containers',
                {
                    context: context,
                    'context-id': groupId,
                },
                {
                    include: '',
                }
            );

            container.value = response.data;
            return container.value.id;
        } catch (error) {
            console.error(`Fehler beim Laden/Erstellen des Containers für Gruppe ${groupId}:`, error);
            container.value = null;
            throw error;
        } finally {
            isLoading.value = false;
        }
    }

    async function fetchContainer(containerId, params = {}) {
        isLoading.value = true;
        try {
            const response = await api.fetch(`dashboard-widget-containers/${containerId}`, {
                params: params,
            });

            container.value = response.data;
        } catch (error) {
            console.error(`Fehler beim Laden des Containers ${containerId}:`, error);
            container.value = null;
            throw error;
        } finally {
            isLoading.value = false;
        }
    }

    async function fetchContainerWidgets(containerId) {
        isLoading.value = true;
        widgetStore.clearRecords();
        try {
            const { data } = await api.get(`dashboard-widget-containers/${containerId}/dashboard-widgets`);
            data.forEach((item) => {
                if (item.type === 'dashboard-widgets') {
                    widgetStore.storeRecord(item);
                }
            });
        } catch (error) {
            console.error(`Fehler beim Laden der Widgets für Container ${containerId}:`, error);
            throw error;
        } finally {
            isLoading.value = false;
        }
    }

    // async function saveLayout(containerId, newLayout) {
    //     try {
    //         const response = await api.update('dashboard-widget-containers', {
    //             id: containerId,
    //             attributes: {
    //                 payload: newLayout,
    //             },
    //         });
    //         container.value = response.data;
    //     } catch (error) {
    //         console.error('Fehler beim Speichern des Container-Layouts:', error);
    //         throw error;
    //     }
    // }

    async function addWidget(widgetType, widgetScope, payload, position, breakpoint) {
        const containerId = container.value.id;

        const newWidgetData = {
            'widget-type': widgetType,
            'widget-scope': widgetScope,
            payload: payload,
            position: position,
            breakpoint: breakpoint,
        };

        try {
            await api.create(`dashboard-widget-containers/${containerId}/dashboard-widgets`, newWidgetData);
            await fetchContainerWidgets(container.value.id);
            await fetchContainer(container.value.id);
        } catch (error) {
            console.error('Fehler beim Hinzufügen des Widgets:', error);
            throw error;
        }
    }

    async function deleteWidget(containerId, widgetId) {
        try {
            await api.delete(`dashboard-widget-containers/${containerId}/dashboard-widgets`, widgetId);
            await fetchContainerWidgets(container.value.id);
            await fetchContainer(container.value.id);
        } catch (error) {
            console.error('Fehler beim Löschen des Widgets:', error);
            throw error;
        }
    }

    async function updateLayout(breakpoint) {
        const id = containerId.value;
        try {
            await api.patch('dashboard-widget-containers', {
                id: id,
                layout: layouts.value[breakpoint],
                breakpoint: breakpoint,
            });
            await fetchContainer(id);
        } catch (error) {
            console.error('Fehler beim update des Layouts:', error);
            throw error;
        }
    }

    return {
        container,
        isLoading,
        containerId,
        layouts,
        hasLayout,
        layoutForBreakpoint,
        fetchOrCreateContainer,
        fetchContainer,
        fetchContainerWidgets,
        addWidget,
        deleteWidget,

        updateLayout,
    };
});
