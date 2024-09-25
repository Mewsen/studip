import CoursewareModule from './store/courseware/courseware.module';
import CoursewareStructureModule from './store/courseware/structure.module';
import FileChooserStore from './store/file-chooser.js';
import CoursewareStructuralElement from './components/courseware/structural-element/CoursewareStructuralElement.vue';
import CoursewareTasksModule from './store/courseware/courseware-tasks.module';
import IndexApp from './components/courseware/IndexApp.vue';
import PluginManager from './components/courseware/plugin-manager.js';
import { StockImagesPlugin } from './plugins/stock-images.js';
import { createRouter, createWebHashHistory } from 'vue-router';
import { h } from "vue";

const mountApp = async (STUDIP, c, store, element) => {
    // get id of parent structural element
    let elem_id = null;
    let entry_id = null;
    let entry_type = null;
    let unit_id = null;
    let licenses = null;
    let elem;
    let feedbackSettings = null;

    if ((elem = document.getElementById(element.substring(1))) !== undefined) {
        if (elem.attributes !== undefined) {
            if (elem.attributes['entry-element-id'] !== undefined) {
                elem_id = elem.attributes['entry-element-id'].value;
            }

            if (elem.attributes['entry-type'] !== undefined) {
                entry_type = elem.attributes['entry-type'].value;
            }

            if (elem.attributes['entry-id'] !== undefined) {
                entry_id = elem.attributes['entry-id'].value;
            }

            if (elem.attributes['unit-id'] !== undefined) {
                unit_id = elem.attributes['unit-id'].value;
            }

            // we need a route for License SORM
            if (elem.attributes['licenses'] !== undefined) {
                licenses = JSON.parse(elem.attributes['licenses'].value);
            }

            if (elem.attributes['feedback-settings'] !== undefined) {
                feedbackSettings = JSON.parse(elem.attributes['feedback-settings'].value);
            }
        }
    }

    const { createApp, httpClient } = await STUDIP.Vue.load();

    let base = new URL(
        STUDIP.URLHelper.parameters.cid
            ? STUDIP.URLHelper.getURL('dispatch.php/course/courseware/courseware/' + unit_id, { cid: STUDIP.URLHelper.parameters.cid }, true)
            : STUDIP.URLHelper.getURL('dispatch.php/contents/courseware/courseware/' + unit_id)
    );
    if (entry_type === 'courses') {
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


    const router = createRouter({
        history: createWebHashHistory(base.toString()),
        routes
    });

    store.registerModule('courseware', CoursewareModule);
    store.registerModule('courseware-structure', CoursewareStructureModule);
    store.registerModule('file-chooser', FileChooserStore);
    store.registerModule('tasks', CoursewareTasksModule);

    httpClient.get('studip/properties')
        .then(response => {
            response.data.data.forEach(prop => {
                store.dispatch('studip-properties/storeRecord', prop);
            });
        });

    store.dispatch('setUrlHelper', STUDIP.URLHelper);
    store.dispatch('setUserId', STUDIP.USER_ID);
    await store.dispatch('users/loadById', {id: STUDIP.USER_ID});
    store.dispatch('setHttpClient', httpClient);

    store.dispatch('coursewareContext', {
        id: entry_id,
        type: entry_type,
        unit: unit_id
    });

    if (entry_type === 'courses') {
        store.dispatch('loadProgresses');
        await store.dispatch('setFeedbackSettings', feedbackSettings);
        await store.dispatch('courseware-units/loadById', { id: unit_id, options: {include: 'structural-element'} });
    }

    store.dispatch('coursewareCurrentElement', elem_id);

    store.dispatch('licenses', licenses);
    store.dispatch('courseware-templates/loadAll');
    store.dispatch('loadUserClipboards', STUDIP.USER_ID);

    const pluginManager = new PluginManager();
    store.dispatch('setPluginManager', pluginManager);
    STUDIP.eventBus.emit('courseware:init-plugin-manager', pluginManager);

    STUDIP.JSUpdater.register(
        'coursewareclipboard',
        () => { store.dispatch('loadUserClipboards', STUDIP.USER_ID)},
        () => { return { 'counter' : store.getters['courseware-clipboards/all'].length };},
        5000
    );

    const app = createApp({
        compatConfig: {
            RENDER_FUNCTION: false,
        },
        render: () => h(IndexApp),
    });

    app.use(router);
    app.use(StockImagesPlugin, { store });
    app.mount(element);

    return app;
};

export default mountApp;
