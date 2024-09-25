import ContentReleasesApp from './components/courseware/ContentReleasesApp.vue';
import CoursewareModule from './store/courseware/courseware.module';
import { h } from "vue";

const mountApp = (STUDIP, createApp, store, element) => {
    store.registerModule('courseware', CoursewareModule);

    let entry_id = null;
    let entry_type = null;
    let elem = document.getElementById(element.substring(1));

    if (elem !== undefined) {
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
    store.dispatch('coursewareContext', {
        id: entry_id,
        type: entry_type,
    });

    store.dispatch('courseware-public-links/loadAll', {
        options: {
            include: 'structural-element',
        },
    });
    store.dispatch('courseware-structural-elements-released/loadAll', {});

    const app = createApp({
        compatConfig: {
            RENDER_FUNCTION: false,
        },
        render: () => h(ContentReleasesApp),
    });
    app.mount(element);

    return app;
}

export default mountApp;
