import Vue, { createApp as vueCreateApp } from 'vue';
import { createStore as vuexCreateStore } from 'vuex';
import eventBus from '../lib/event-bus.ts';
import gettext from '../lib/gettext';
import PortalVue from 'portal-vue';
import BaseComponents from '../../../vue/base-components.js';
import BaseDirectives from "../../../vue/base-directives.js";
import StudipStore from "../../../vue/store/StudipStore.js";
// import CKEditor from '@ckeditor/ckeditor5-vue2';
import { resourceModule } from '@/assets/javascripts/lib/reststate-vuex.js';
import axios from 'axios';

const getHttpClient = () =>
    axios.create({
        baseURL: STUDIP.URLHelper.getURL(`jsonapi.php/v1`, {}, true),
        headers: {
            'Content-Type': 'application/vnd.api+json',
        },
    });

const httpClient = getHttpClient();

const createStore = () => {
    const store = vuexCreateStore({});

    store.registerModule('studip', StudipStore);

    STUDIP.jsonapi_schemas.forEach((name) => {
        store.registerModule(name, resourceModule({ name, httpClient }));
    });

    return store;
}

// Setup gettext
eventBus.on('studip:set-locale', (locale) => {
    Vue.config.language = locale;
})

// Setup store
const store = createStore();

// Vue.use(CKEditor);

// Define createApp function
function createApp(options, ...args) {
//    Vue.config.language = getLocale();
    const app = vueCreateApp({ store, ...options }, ...args);

    app.config.compilerOptions.whitespace = 'condense';

    // Define our own global mixin for Vue
    app.mixin({
        methods: {
            globalEmit(...args) {
                eventBus.emit(...args);
            },
            globalOn(...args) {
                eventBus.on(...args);
            },
            globalOff(...args) {
                eventBus.off(...args);
            },
            getStudipConfig: store.getters['studip/getConfig']
        },
    });

    app.use(gettext);
    app.use(PortalVue);
    app.use(store);

    // Register global components and directives
    registerGlobalComponents(app);
    registerGlobalDirectives(app);

    if (options.el) {
        app.mount(options.el);
    }
    return app;
}

// Define global registration functions for components and directives
function registerGlobalComponents(app) {
    for (const [name, component] of Object.entries(BaseComponents)) {
        app.component(name, component);
    }
}

function registerGlobalDirectives(app) {
    for (const [name, directive] of Object.entries(BaseDirectives)) {
        app.directive(name, directive);
    }
}

export { createApp, eventBus, store, httpClient };
