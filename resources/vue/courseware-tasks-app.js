import TaskGroupsIndex from './components/courseware/tasks/PagesTaskGroupsIndex.vue';
import TaskGroupsShow from './components/courseware/tasks/PagesTaskGroupsShow.vue';
import { createRouter, RouterView, createWebHashHistory } from 'vue-router';
import CoursewareModule from './store/courseware/courseware.module';
import CoursewareTasksModule from './store/courseware/courseware-tasks.module';
import CoursewareStructureModule from './store/courseware/structure.module';
import axios from 'axios';
import {h} from "vue";

const mountApp = async (STUDIP, createApp, store, element) => {
    const getHttpClient = () =>
        axios.create({
            baseURL: STUDIP.URLHelper.getURL(`jsonapi.php/v1`, {}, true),
            headers: {
                'Content-Type': 'application/vnd.api+json',
            },
        });

    const httpClient = getHttpClient();

    const routes = [
        {
            path: '/',
            name: 'task-groups-index',
            component: TaskGroupsIndex,
        },
        {
            path: '/task-groups/:id',
            name: 'task-groups-show',
            component: TaskGroupsShow,
            props: true,
        },
    ];

    const base = new URL(
        window.STUDIP.URLHelper.getURL(
            'dispatch.php/course/courseware/tasks',
            { cid: STUDIP.URLHelper.parameters.cid },
            true
        )
    );
    const router = createRouter({
        history: createWebHashHistory(),
        routes,
    });
    router.beforeEach((to, from, next) => {
        if (to?.query?.cid !== undefined) {
            next();
        } else {
            next({ ...to, query: { ...to.query, cid: window.STUDIP.URLHelper.parameters.cid } });
        }
    });

    store.registerModule('courseware', CoursewareModule);
    store.registerModule('tasks', CoursewareTasksModule);
    store.registerModule('courseware-structure', CoursewareStructureModule);

    let entry_id = null;
    let entry_type = null;
    let isTeacher = false;
    let elem;

    if ((elem = document.getElementById(element.substring(1))) !== undefined) {
        if (elem.attributes !== undefined) {
            if (elem.attributes['entry-type'] !== undefined) {
                entry_type = elem.attributes['entry-type'].value;
            }

            if (elem.attributes['entry-id'] !== undefined) {
                entry_id = elem.attributes['entry-id'].value;
            }

            if (elem.attributes['is-teacher'] !== undefined) {
                isTeacher = JSON.parse(elem.attributes['is-teacher'].value);
            }
        }
    }

    store.dispatch('setUserId', STUDIP.USER_ID);
    await store.dispatch('users/loadById', { id: STUDIP.USER_ID });
    store.dispatch('setUserIsTeacherInCourse', isTeacher);
    store.dispatch('setHttpClient', httpClient);
    store.dispatch('coursewareContext', {
        id: entry_id,
        type: entry_type,
    });
    await store.dispatch('tasks/loadTasksOfCourse', { cid: entry_id });

    const app = createApp({
        render: () => h(RouterView),
    });
    app.use(router);
    app.mount(element);

    return app;
};

export default mountApp;
