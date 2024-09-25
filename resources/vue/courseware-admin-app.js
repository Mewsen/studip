import AdminApp from './components/courseware/AdminApp.vue';
import CoursewareAdminModule from './store/courseware/courseware-admin.module';
import { h } from "vue";

const mountApp = (STUDIP, createApp, store, element) => {
    store.registerModule('courseware', CoursewareAdminModule);

    store.dispatch('courseware-templates/loadAll');

    const app = createApp({
        compatConfig: {
            RENDER_FUNCTION: false,
        },
        render: () => h(AdminApp),
    });
    app.mount(element);

    return app;
}

export default mountApp;
