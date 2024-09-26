import CoursewareShelfModule from './store/courseware/courseware-shelf.module';
import ShelfApp from './components/courseware/ShelfApp.vue';
import { resourceModule } from '@/assets/javascripts/lib/reststate-vuex.js';
import { StockImagesPlugin } from './plugins/stock-images.js';
import { h } from 'vue';

const mountApp = async (STUDIP, c, store, element) => {
    // handle studip 5.0 to 5.2 urls
    const elemId = window.location.hash.match(/structural_element\/(\d+)/);

    if (elemId) {
        let url = new URL(window.location.href);
        url.searchParams.set('element_id', elemId[1]);
        window.location.href = url;

        return false;
    }

    let elem;
    let entry_id = null;
    let entry_type = null;
    let licenses = null;
    let feedbackSettings = null;
    let isTeacher = false;

    if ((elem = document.getElementById(element.substring(1))) !== undefined) {
        if (elem.attributes !== undefined) {
            if (elem.attributes['entry-type'] !== undefined) {
                entry_type = elem.attributes['entry-type'].value;
            }

            if (elem.attributes['entry-id'] !== undefined) {
                entry_id = elem.attributes['entry-id'].value;
            }

            if (elem.attributes['licenses'] !== undefined) {
                licenses = JSON.parse(elem.attributes['licenses'].value);
            }
            if (elem.attributes['feedback-settings'] !== undefined) {
                feedbackSettings = JSON.parse(elem.attributes['feedback-settings'].value);
            }
            if (elem.attributes['is-teacher'] !== undefined) {
                isTeacher = JSON.parse(elem.attributes['is-teacher'].value);
            }
        }
    }

    const { createApp, httpClient } = await STUDIP.Vue.load();
    store.registerModule('courseware-shelf', CoursewareShelfModule);
    store.registerModule(
        'courseware-structural-elements-shared',
        resourceModule({
            name: 'courseware-structural-elements-shared',
            httpClient
        })
    );

    store.dispatch('setUrlHelper', STUDIP.URLHelper);
    store.dispatch('setHttpClient', httpClient);
    store.dispatch('setLicenses', licenses);
    store.dispatch('setUserId', STUDIP.USER_ID);
    await store.dispatch('users/loadById', {id: STUDIP.USER_ID});
    store.dispatch('setContext', {
        id: entry_id,
        type: entry_type,
    });
    if (entry_type === 'courses') {
        store.dispatch('setUserIsTeacher', isTeacher);
        await store.dispatch('loadCourseUnits', entry_id);
        await store.dispatch('setFeedbackSettings', feedbackSettings);
    } else {
        await store.dispatch('loadUserUnits', entry_id);
        await store.dispatch('courseware-structural-elements-shared/loadAll', { options: { include: 'owner' } });
    }

    const app = createApp({
        render: () => h(ShelfApp),
    });
    app.use(StockImagesPlugin, { store });
    app.mount(element);

};

export default mountApp;
