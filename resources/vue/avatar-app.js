import AvatarApp from './components/avatar/AvatarApp.vue';
import AvatarModule from './store/avatar.module';
import axios from 'axios';
import { h } from 'vue';

const mountApp = async (STUDIP, createApp, store, element) => {

    let entry_id = null;
    let entry_type = null;
    let elem = document.getElementById(element.substring(1));

    if (elem) {
        entry_type = elem.attributes?.['entry-type']?.value ?? null;
        entry_id = elem.attributes?.['entry-id']?.value ?? null;
    }

    const httpClient = axios.create({
        baseURL: STUDIP.URLHelper.getURL(`jsonapi.php/v1`, {}, true),
        headers: {
            'Content-Type': 'application/vnd.api+json',
        },
    });

    store.registerModule('avatar-module', AvatarModule);

    const context = {
        type: entry_type,
        id: entry_id
    }
    await store.dispatch('setUserId', STUDIP.USER_ID);
    await store.dispatch('users/loadById', { id: STUDIP.USER_ID });
    await store.dispatch('setHttpClient', httpClient);
    await store.dispatch('setContext', context);
    await store.dispatch('loadAvatar');

    const app =  createApp({
        compatConfig: {
            RENDER_FUNCTION: false,
        },
        render: () => h(AvatarApp),
        store,
    });
    app.mount(element);

    return app;
}

export default mountApp;
