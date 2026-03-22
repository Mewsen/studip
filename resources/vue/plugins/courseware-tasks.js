import { createRouter, createWebHashHistory } from 'vue-router';
import TaskGroupsIndex from '@/vue/components/courseware/tasks/PagesTaskGroupsIndex.vue';
import TaskGroupsShow from '@/vue/components/courseware/tasks/PagesTaskGroupsShow.vue';

export const CoursewareTasksApp = {
    install(app, options = {}) {
        if (!('store' in options)) {
            throw new Error('You must provide the vuex store via the options argument');
        }
        if (!('httpClient' in options)) {
            throw new Error('You must provide the httpClient via the options argument');
        }
        const { httpClient, store } = options;

        store.dispatch('setHttpClient', httpClient);

        const router = this.initializeRouter();
        app.use(router);
    },

    initializeRouter() {
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

        const router =  createRouter({ history: createWebHashHistory(), routes });
        router.beforeEach((to, from, next) => {
            if (to?.query?.cid !== undefined) {
                next();
            } else {
                next({ ...to, query: { ...to.query, cid: window.STUDIP.URLHelper.parameters.cid } });
            }
        });

        return router;
    },
};
