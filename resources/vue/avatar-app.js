import AvatarApp from './components/avatar/AvatarApp.vue';
import AvatarModule from './store/avatar.module';
import Vue from 'vue';
import Vuex from 'vuex';
import { mapResourceModules } from '@elan-ev/reststate-vuex';
import axios from 'axios';

const mountApp = async (STUDIP, createApp, element) => {

    let entry_id = null;
    let entry_type = null;
    let avatar_url = null;
    let elem;

    if ((elem = document.getElementById(element.substring(1))) !== undefined) {
        if (elem.attributes !== undefined) {
            if (elem.attributes['entry-type'] !== undefined) {
                entry_type = elem.attributes['entry-type'].value;
            }

            if (elem.attributes['entry-id'] !== undefined) {
                entry_id = elem.attributes['entry-id'].value;
            }

            if (elem.attributes['avatar-url'] !== undefined) {
                avatar_url = elem.attributes['avatar-url'].value;
            }
        }
    }

    const getHttpClient = () =>
    axios.create({
        baseURL: STUDIP.URLHelper.getURL(`jsonapi.php/v1`, {}, true),
        headers: {
            'Content-Type': 'application/vnd.api+json',
        },
    });
    const httpClient = getHttpClient();

    const store = new Vuex.Store({
        modules: {
            'avatar-module': AvatarModule,
            ...mapResourceModules({
                names: [
                    'avatar',
                    'courses',
                    'institutes',
                    'stock-images',
                    'studygroups',
                    'users',
                ],
                httpClient,
            }),
        }
    });

    const context = {
        type: entry_type,
        id: entry_id
    }
    store.dispatch('setUserId', STUDIP.USER_ID);
    await store.dispatch('users/loadById', { id: STUDIP.USER_ID });
    store.dispatch('setHttpClient', httpClient);
    store.dispatch('setContext', context);
    const avatar = await store.dispatch('loadAvatar');

    const app =  createApp({
        render: (h) => h(AvatarApp),
        store,
    });
    app.$mount(element);

    return app;
}

export default mountApp;