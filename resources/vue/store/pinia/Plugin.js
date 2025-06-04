import { defineStore } from 'pinia';

export const usePluginStore = defineStore('plugins', {
    state: () => ({
        plugins: [],
        updateInfos: [],
    }),
    getters: {
        getPluginById: (state) => (pluginId) => state.plugins.find(plugin => plugin.id === pluginId),
        allPlugins(state) {
            return state.plugins;
        },
        origins(state) {
            return state.plugins.reduce(
                (origins, plugin) => {
                    if (plugin.manifest.origin && !origins.includes(plugin.manifest.origin)) {
                        origins.push(plugin.manifest.origin);
                    }
                    return origins;
                },
                []
            ).sort((a, b) => a.localeCompare(b))
        },
        types(state) {
            return state.plugins.reduce(
                (types, plugin) => [...types, ...plugin.type ?? []],
                []
            ).filter((type, index, types) => types.indexOf(type) === index)
            .sort((a, b) => a.localeCompare(b))
        }
    },
    actions: {
        async loadPlugins() {
            this.plugins = await STUDIP.jsonapi.withPromises().get('plugins').then(response => {
                return response.data.map(plugin => {
                    return {
                        id: plugin.id,
                        ...plugin.attributes,
                        ...plugin.meta
                    }
                });
            })
        },
        async loadUpdateInfos() {
            this.updateInfos = await STUDIP.jsonapi.withPromises().get('plugins/updates');
        },
        changeConfig(key, value) {
            const documentId = `${STUDIP.USER_ID}_${key}`;

            const data = {
                id: documentId,
                type: 'config-values',
                attributes: { value }
            };

            return STUDIP.jsonapi.withPromises().patch(`config-values/${documentId}`, { data: { data } })
        }
    }
});
