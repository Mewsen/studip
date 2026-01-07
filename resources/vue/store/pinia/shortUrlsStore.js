import {defineStore} from 'pinia';
import {ref} from 'vue';
import {$gettext} from '../../../assets/javascripts/lib/gettext';

export const useShortUrlsStore = defineStore('shortUrls', () => {
    let shortUrls = ref([]);

    async function initialize() {
        await STUDIP.jsonapi.withPromises().get('short-urls')
            .then(response => {
                shortUrls.value = response.data;
            })
            .catch(error => STUDIP.Report.error($gettext('Fehler beim Laden der Kurzlinks'), error));
    }

    function getShortUrls() {
        return shortUrls.value;
    }

    function getShortUrl(id) {
        return shortUrls.value.find(item => item.id === id);
    }

    async function storeShortUrl(shortUrl) {
        const index = shortUrls.value.findIndex(item => item.id === shortUrl.id);
        let response = {};

        // Not found in store, create a new entry.
        if (index === -1) {
            response = await STUDIP.jsonapi.withPromises().post('short-urls', {data: {data: shortUrl}});
            shortUrls.value.push(response.data);
        } else {
            response = await STUDIP.jsonapi.withPromises().patch(`short-urls/${shortUrl.id}`, {data: {data: shortUrl}});
            shortUrls.value[index] = response.data;
        }

        STUDIP.Report.success($gettext('Der Kurzlink wurde gespeichert.'));
        return response.data;
    }

    function deleteShortUrl(id) {
        STUDIP.jsonapi.withPromises().delete(`short-urls/${id}`)
            .then(() => shortUrls.value.splice(shortUrls.value.findIndex(item => item.id === id), 1))
            .catch(error => STUDIP.Report.error($gettext('Fehler beim Löschen des Kurzlinks'), error));
    }

    return {
        initialize,
        getShortUrl,
        getShortUrls,
        storeShortUrl,
        deleteShortUrl
    };
});
