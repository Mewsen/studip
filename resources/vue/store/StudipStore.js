import { eventBus, store } from '../../assets/javascripts/chunks/vue';

const studipStore = {
    namespaced: true,

    state() {
        return { ...STUDIP.config, consumeMode: false };
    },
    getters: {
        getConfig: (state) => (key) => {
            if (state[key] === undefined) {
                throw new Error(`Invalid access to unknown configuration item "${key}"`);
            }
            return state[key];
        },
    },
};

// Make the current state of "focus mode" (fullscreen) available to Vue components.
eventBus.on('switch-focus-mode', (mode) => {
    store.state.studip.consumeMode = mode;
});

export default studipStore;
