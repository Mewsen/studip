import PublicApp from './components/courseware/PublicApp.vue';
import CoursewarePublicModule from './store/courseware/courseware-public.module';
import PublicCoursewareStructuralElement from './components/courseware/structural-element/PublicCoursewareStructuralElement.vue';
import CoursewarePublicStructureModule from './store/courseware/public-structure.module';
import PluginManager from './components/courseware/plugin-manager.js';
import { createRouter, createWebHashHistory } from 'vue-router';
import axios from 'axios';
import { h } from 'vue';

const mountApp = (STUDIP, createApp, store, element) => {
    const getHttpClient = () =>
        axios.create({
            baseURL: STUDIP.URLHelper.getURL(`jsonapi.php/v1`, {}, true),
            headers: {
                'Content-Type': 'application/vnd.api+json',
            },
        });

    const httpClient = getHttpClient();

    let elem_id = null;
    let link_id = null;
    let link_pass = null;
    let entry_type = null;
    let block_types = [];
    let container_types = [];
    let elem = document.getElementById(element.substring(1));

    if (elem !== undefined) {
        if (elem.attributes !== undefined) {
            if (elem.attributes['entry-element-id'] !== undefined) {
                elem_id = elem.attributes['entry-element-id'].value;
            }

            if (elem.attributes['entry-type'] !== undefined) {
                entry_type = elem.attributes['entry-type'].value;
            }

            if (elem.attributes['link-id'] !== undefined) {
                link_id = elem.attributes['link-id'].value;
            }

            if (elem.attributes['link-pass'] !== undefined) {
                link_pass = elem.attributes['link-pass'].value;
            }

            if (elem.attributes['block-types'] !== undefined) {
                block_types = JSON.parse(elem.attributes['block-types'].value);
            }

            if (elem.attributes['container-types'] !== undefined) {
                container_types = JSON.parse(elem.attributes['container-types'].value);
            }
        }
    }

    let base = new URL(STUDIP.URLHelper.getURL('dispatch.php/courseware/public', { link: link_id }, true));

    store.registerModule('courseware-public', CoursewarePublicModule);
    store.registerModule('courseware-structure', CoursewarePublicStructureModule);
    store.dispatch('setContext', {
        id: link_id,
        type: entry_type,
        rootId: elem_id,
    });
    store.dispatch('setHttpClient', httpClient);

    if (link_pass) {
        store.dispatch('setPassword', link_pass);
    } else {
        store.dispatch('setIsAuthenticated', true);
    }

    store.dispatch('setBlockTypes', block_types);
    store.dispatch('setContainerTypes', container_types);

    const pluginManager = new PluginManager();
    store.dispatch('setPluginManager', pluginManager);
    STUDIP.eventBus.emit('courseware:init-plugin-manager', pluginManager);

    const routes = [
        {
            path: '/',
            redirect: '/structural_element/' + elem_id,
        },
        {
            path: '/structural_element/:id',
            name: 'PublicCoursewareStructuralElement',
            component: PublicCoursewareStructuralElement,
            beforeEnter: (to, from, next) => {
                if (!store.getters.isAuthenticated) {
                    return false;
                }
                next();
            },
        },
    ];

    const router = createRouter({
        base: `${base.pathname}${base.search}`,
        history: createWebHashHistory(),
        routes,
    });

    const app = createApp({
        render: () => h(PublicApp),
    });
    app.use(router);
    app.mount(element);

    return app;
};

export default mountApp;
