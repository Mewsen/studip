import { ref, computed } from 'vue';
import { defineStore } from 'pinia';
import { api } from '../kitsu-api.js';

export const useContainerStore = defineStore('containerStore', () => {
    const container = ref(null);
    const isLoading = ref(false);

    const getContainerId = computed(() => container.value?.id);

    const getLayout = computed(() => container.value?.payload || {});

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

    async function fetchContainer(containerId) {
        isLoading.value = true;
        try {
            const response = await api.fetch('dashboard-widget-containers', containerId);
            container.value = response.data;
        } catch (error) {
            console.error(`Fehler beim Laden des Containers ${containerId}:`, error);
            container.value = null;
            throw error;
        } finally {
            isLoading.value = false;
        }
    }

    async function saveLayout(containerId, newLayout) {
        try {
            const response = await api.update('dashboard-widget-containers', {
                id: containerId,
                attributes: {
                    payload: newLayout,
                },
            });
            container.value = response.data;
        } catch (error) {
            console.error('Fehler beim Speichern des Container-Layouts:', error);
            throw error;
        }
    }

    return {
        container,
        isLoading,
        getContainerId,
        getLayout,
        hasLayout,
        fetchOrCreateContainer,
        fetchContainer,
        saveLayout,
    };
});
