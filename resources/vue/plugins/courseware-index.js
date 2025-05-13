import { createRouter, createWebHashHistory } from 'vue-router';
import PluginManager from '@/vue/components/courseware/plugin-manager.js';
import CoursewareStructuralElement from '@/vue/components/courseware/structural-element/CoursewareStructuralElement.vue';

export const CoursewareIndexApp = {
    install(app, options = {}) {
        if (!('store' in options)) {
            throw new Error('You must provide the vuex store via the options argument');
        }
        if (!('httpClient' in options)) {
            throw new Error('You must provide the httpClient via the options argument');
        }

        const { httpClient, store } = options;
        store.dispatch('setHttpClient', httpClient);

        const router = this.initializeRouter(store);
        app.use(router);

        const pluginManager = new PluginManager();
        store.dispatch('setPluginManager', pluginManager);
        STUDIP.eventBus.emit('courseware:init-plugin-manager', pluginManager);
    },

    initializeRouter(store) {
        const elem_id = store.getters.currentElement;
        const context = store.getters.context;

        const base = new URL(
            STUDIP.URLHelper.parameters.cid
                ? STUDIP.URLHelper.getURL(
                      'dispatch.php/course/courseware/courseware/' + context.unit,
                      { cid: STUDIP.URLHelper.parameters.cid },
                      true,
                  )
                : STUDIP.URLHelper.getURL('dispatch.php/contents/courseware/courseware/' + context.unit),
        );
        if (context.type === 'courses') {
            base.search += '&';
        }

        const routes = [
            {
                path: '/',
                redirect: '/structural_element/' + elem_id,
            },
            {
                path: '/structural_element/:id',
                name: 'CoursewareStructuralElement',
                component: CoursewareStructuralElement,
            },
        ];

        return createRouter({
            history: createWebHashHistory(base.toString()),
            routes,
        });
    },
};
