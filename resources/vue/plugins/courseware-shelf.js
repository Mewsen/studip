import { resourceModule } from '@/assets/javascripts/lib/reststate-vuex.js';

export const CoursewareShelfApp = {
    install(app, options = {}) {
        if (!('store' in options)) {
            throw new Error('You must provide the vuex store via the options argument');
        }
        if (!('httpClient' in options)) {
            throw new Error('You must provide the httpClient via the options argument');
        }
        const { httpClient, store } = options;

        store.dispatch('setHttpClient', httpClient);
        store.registerModule(
            'courseware-structural-elements-shared',
            resourceModule({ name: 'courseware-structural-elements-shared', httpClient }),
        );
    },
};
