import Vue, { createApp as vueCreateApp } from 'vue';
import Vuex from 'vuex';
import Router from "vue-router";
import eventBus from '../lib/event-bus.ts';
import gettext from '../lib/gettext';
import PortalVue from 'portal-vue';
import BaseComponents from '../../../vue/base-components.js';
import BaseDirectives from "../../../vue/base-directives.js";
import StudipStore from "../../../vue/store/StudipStore.js";
// import CKEditor from '@ckeditor/ckeditor5-vue2';

// Setup gettext
eventBus.on('studip:set-locale', (locale) => {
    Vue.config.language = locale;
})

// Register global components and directives
registerGlobalComponents();
registerGlobalDirectives();

// Setup store and default Stud.IP store
Vue.use(Vuex);
const store = new Vuex.Store({});

store.registerModule('studip', StudipStore);

// Setup router and PortalVue
Vue.use(Router);
Vue.use(PortalVue);

// Define our own global mixin for Vue
Vue.mixin({
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

// Vue.use(CKEditor);

// Define createApp function
function createApp(options, ...args) {
//    Vue.config.language = getLocale();
    const app = vueCreateApp({ store, ...options }, ...args);
    app.use(gettext);
    if (options.el) {
        app.mount(options.el);
    }
    return app;
}

// Define global registration functions for components and directives
function registerGlobalComponents() {
    for (const [name, component] of Object.entries(BaseComponents)) {
        Vue.component(name, component);
    }
}

function registerGlobalDirectives() {
    for (const [name, directive] of Object.entries(BaseDirectives)) {
        Vue.directive(name, directive);
    }
}

export { createApp, eventBus, store };
