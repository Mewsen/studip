import {defineStore} from 'pinia';
import {ref} from 'vue';
import {$gettext} from '../../../assets/javascripts/lib/gettext';
import Sqids from 'sqids';

export const useShortUrlsStore = defineStore('shortUrls', () => {
    const loading = ref(false);
    const shortUrls = ref({});
    const editShortUrl = ref(null);

    function startEditShortUrl(shortUrl = null, defaultPath = '') {
        if (shortUrl) {
            editShortUrl.value = {
                id: shortUrl.id,
                attributes: {
                    alias: shortUrl.attributes.alias,
                    path: shortUrl.attributes.path
                }
            };
        } else {
            const sqids = new Sqids();
            const randomNumbers = Array.from({length: 3}, () => Math.floor(Math.random() * 1000));
            const alias = sqids.encode(randomNumbers);

            editShortUrl.value = {
                attributes: {
                    alias,
                    path: defaultPath
                }
            };
        }
    }

    function resetEditShortUrl() {
        editShortUrl.value = null;
    }

    async function fetchShortUrls() {
        loading.value = true;
        try {
            shortUrls.value = await STUDIP.jsonapi.withPromises().get('short-urls', {data: {}});
        } catch (error) {
            shortUrls.value = [];
            STUDIP.Report.error($gettext('Es ist ein Fehler aufgetreten'), error);
        } finally {
            loading.value = false;
        }
    }

    async function updateShortUrl(id, payload) {
        const response = await STUDIP.jsonapi.withPromises().patch(`short-urls/${id}`, {data: payload});
        const updatedItem = response.data;

        const index = shortUrls.value.data.findIndex(item => item.id === id);
        if (index !== -1) {
            shortUrls.value.data[index] = updatedItem;
        }

        return updatedItem;
    }

    async function saveShortUrl() {
        const id = editShortUrl.value?.id;
        const {alias, path} = editShortUrl.value.attributes;

        if (!alias || alias.length < 4) {
            STUDIP.Report.error($gettext('Alias muss mindestens 4 Zeichen lang sein.'));
            return;
        }

        const payload = {
            data: {
                attributes: {alias, path}
            }
        };

        const shortUrl = getShortUrl(alias);

        try {
            if (id) {
                const response = await STUDIP.jsonapi.withPromises().patch(`short-urls/${id}`, {data: payload});
                const updatedItem = response.data;
                const index = shortUrls.value.data.findIndex(item => item.id === id);
                if (index !== -1) {
                    shortUrls.value.data[index] = updatedItem;
                }
            } else {
                console.log(payload);
                await STUDIP.jsonapi.withPromises().post('short-urls', {data: payload});
                await fetchShortUrls();
            }

            STUDIP.Report.success($gettext('Die Kurz-URL wurde gespeichert. Sie finden den Link in der Zwischenablage'));
            await navigator.clipboard.writeText(shortUrl);
            STUDIP.Dialog.close();
        } catch (error) {
            const detail = error?.jqXhr?.responseJSON?.errors?.[0]?.detail || '';
            STUDIP.Report.error($gettext('Speichern der Kurz-URL fehlgeschlagen.'), detail);
        }
    }

    function deleteShortUrl(id) {
        STUDIP.Dialog.confirm(
            $gettext('Soll diese Kurz-URL wirklich gelöscht werden?'),
            async () => {
                try {
                    await STUDIP.jsonapi.withPromises().delete(`short-urls/${id}`);
                    STUDIP.Report.success($gettext('Kurz-URL wurde gelöscht.'));

                    if (shortUrls.value?.data) {
                        shortUrls.value.data = shortUrls.value.data.filter(item => item.id !== id);
                    }
                } catch (error) {
                    STUDIP.Report.error($gettext('Löschen fehlgeschlagen'), error);
                }
            },
            STUDIP.Dialog.close()
        );
    }

    function getShortUrl(alias) {
        return STUDIP.URLHelper.getURL('dispatch.php/u/r/' + alias,  {}, true);
    }

    return {
        loading,
        shortUrls,
        fetchShortUrls,
        deleteShortUrl,
        updateShortUrl,
        startEditShortUrl,
        saveShortUrl,
        resetEditShortUrl,
        editShortUrl,
        getShortUrl
    };
});
