import ActivitiesApp from './components/courseware/ActivitiesApp.vue';
import CoursewareModule from './store/courseware/courseware.module';
import CoursewareActivitiesModule from './store/courseware/courseware-activities.module';
import CoursewareStructureModule from './store/courseware/structure.module';
import axios from 'axios';
import { h } from "vue";

const mountApp = async (STUDIP, createApp, store, element) => {
    const getHttpClient = () =>
        axios.create({
            baseURL: STUDIP.URLHelper.getURL(`jsonapi.php/v1`, {}, true),
            headers: {
                'Content-Type': 'application/vnd.api+json',
            },
        });

    const httpClient = getHttpClient();

    store.registerModule('courseware', CoursewareModule);
    store.registerModule('courseware-structure', CoursewareStructureModule);
    store.registerModule('courseware-activities', CoursewareActivitiesModule);

    let entry_id = null;
    let entry_type = null;
    let elem;

    if ((elem = document.getElementById(element.substring(1))) !== undefined) {
        if (elem.attributes !== undefined) {
            if (elem.attributes['entry-type'] !== undefined) {
                entry_type = elem.attributes['entry-type'].value;
            }

            if (elem.attributes['entry-id'] !== undefined) {
                entry_id = elem.attributes['entry-id'].value;
            }
        }
    }

    store.dispatch('setUserId', STUDIP.USER_ID);
    await store.dispatch('users/loadById', {id: STUDIP.USER_ID});
    store.dispatch('setHttpClient', httpClient);
    store.dispatch('coursewareContext', {
        id: entry_id,
        type: entry_type,
    });
    await store.dispatch('loadCourseUnits', entry_id);

    const app = createApp({
        compatConfig: {
            RENDER_FUNCTION: false,
        },
        render: () => h(ActivitiesApp),
    });
    app.mount(element);

    return app;
};

export default mountApp;
