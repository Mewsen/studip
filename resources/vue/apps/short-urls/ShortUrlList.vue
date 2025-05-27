<template>
    <studip-progress-indicator v-if="store.loading" :size="32"/>

    <table v-else-if="store.shortUrls.data?.length > 0" class="default">
        <colgroup>
            <col>
            <col>
            <col style="width: 20px">
        </colgroup>
        <thead>
        <tr>
            <th>{{ $gettext('Bezeichnung') }}</th>
            <th>{{ $gettext('Kurz-URL') }}</th>
            <th>{{ $gettext('URL') }}</th>
            <th>{{ $gettext('Aktionen') }}</th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="(shortUrl, index) in store.shortUrls.data" :key="index">
            <td>{{ shortUrl.attributes.alias }}</td>
            <td><a :href="store.getShortUrl(shortUrl.attributes.alias)">{{ store.getShortUrl(shortUrl.attributes.alias)}}</a></td>
            <td><a :href="getFullURL(shortUrl.attributes.path)"
                   target="_blank">{{ getFullURL(shortUrl.attributes.path) }}</a></td>
            <td class="actions">
                <studip-action-menu
                    :items="actionMenuItems"
                    @edit="editShortUrl(shortUrl)"
                    @delete="store.deleteShortUrl(shortUrl.id)"
                    @copy="copyToClipboard(shortUrl.attributes.alias)"
                />
            </td>
        </tr>
        </tbody>
    </table>

    <studip-message-box v-else type="info">
        {{ $gettext('Es wurden keine Kurz-Urls gefunden.') }}
    </studip-message-box>

    <studip-dialog
        v-if="showEditDialog"
        :title="$gettext('Kurz-URL bearbeiten')"
        :confirmText="$gettext('Aktualisieren')"
        confirmClass="accept"
        :closeText="$gettext('Schließen')"
        closeClass="cancel"
        height="340"
        @close="closeEditDialog"
        @confirm="save"
    >
        <template #dialogContent>
            <short-url :isNew="false"/>
        </template>
    </studip-dialog>
</template>

<script setup>
import {ref, onMounted} from 'vue';
import {$gettext} from '../../../assets/javascripts/lib/gettext';
import {useShortUrlsStore} from '../../store/pinia/shortUrlsStore';

import StudipProgressIndicator from "../../components/StudipProgressIndicator.vue";
import StudipDialog from '../../components/StudipDialog.vue';
import StudipActionMenu from '@/vue/components/StudipActionMenu.vue';
import ShortUrl from '@/vue/apps/short-urls/ShortUrl.vue';

const store = useShortUrlsStore();
const showEditDialog = ref(false);

const actionMenuItems = [
    {label: $gettext('In die Zwischenablage kopieren'), icon: 'clipboard', emit: 'copy'},
    {label: $gettext('Bearbeiten'), icon: 'edit', emit: 'edit'},
    {label: $gettext('Löschen'), icon: 'trash', emit: 'delete'}
];

function copyToClipboard(alias) {
    const shortUrl = store.getShortUrl(alias);
    navigator.clipboard.writeText(shortUrl);
    STUDIP.Report.success($gettext('Sie finden den Link in der Zwischenablage'));
}

function getFullURL(path) {
    return STUDIP.URLHelper.getURL(path);
}

function editShortUrl(url) {
    store.startEditShortUrl(url);
    showEditDialog.value = true;
}

function closeEditDialog() {
    showEditDialog.value = false;
    store.resetEditShortUrl();
}

async function save() {
    await store.saveShortUrl();
    closeEditDialog();
}

onMounted(() => {
    store.fetchShortUrls();
});
</script>
