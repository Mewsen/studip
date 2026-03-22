export const CoursewareActivitiesApp = {
    install(app, options = {}) {
        if (!('store' in options)) {
            throw new Error('You must provide the vuex store via the options argument');
        }
        if (!('httpClient' in options)) {
            throw new Error('You must provide the httpClient via the options argument');
        }
        const { httpClient, store } = options;

        store.dispatch('setHttpClient', httpClient);
    },
};
