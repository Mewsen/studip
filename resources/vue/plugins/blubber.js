import axios from 'axios';
import { mapResourceModules } from '@/assets/javascripts/lib/reststate-vuex.js';
import JSUpdater from '@/assets/javascripts/lib/jsupdater.js';
import blubberModule from '../store/blubber.js';
import * as components from '../components/blubber/components.js';

const JSONAPI_PATH = 'jsonapi.php/v1';

export const BlubberPlugin = {
    install(app, options = {}) {
        if (!('store' in options)) {
            throw new Error('You must provide the vuex store via the options argument');
        }

        this.enhanceStore(options.store);
        this.registerComponents(app);
        this.registerUpdater(options.store);
    },
    enhanceStore(store) {
        const httpClient = getHttpClient(window.STUDIP.URLHelper.getURL(JSONAPI_PATH, {}, true));
        initializeStore(store, httpClient);
    },
    registerComponents(app) {
        Object.entries(components).forEach(([name, component]) => {
            const exists = app.component(name);
            if (!exists) {
                app.component(name, component);
            }
        });
    },
    registerUpdater(store) {
        registerUpdater(JSUpdater, store);
    },
};

function getHttpClient(baseURL) {
    return axios.create({ baseURL, headers: { 'Content-Type': 'application/vnd.api+json' } });
}

function initializeStore(store, httpClient) {
    const modules = mapResourceModules({ names: ['blubber-threads', 'blubber-comments', 'users'], httpClient });
    Object.entries(modules).forEach(([name, module]) => {
        if (!store.hasModule(name)) {
            store.registerModule(name, module);
        }
    });
    if (!store.hasModule(['studip'])) {
        store.registerModule(['studip'], { namespaced: true });
    }
    if (!store.hasModule(['studip', 'blubber'])) {
        store.registerModule(['studip', 'blubber'], blubberModule);
    }
}

function registerUpdater(updater, store) {
    if (!updater.isRegistered('blubber')) {
        updater.register(
            'blubber',
            (datagram) => store.dispatch('studip/blubber/updateState', datagram),
            store.getters['studip/blubber/pollingParams']
        );
    }
}
