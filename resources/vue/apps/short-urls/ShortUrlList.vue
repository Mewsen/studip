<template>
    <studip-progress-indicator v-if="loading" :size="32"/>

    <table v-else-if="store.getShortUrls().length > 0" class="default sortable-table">
        <colgroup>
            <col>
            <col>
            <col>
            <col style="width: 20px">
        </colgroup>
        <thead>
        <tr class="sortable">
            <th :class="getOrderClasses('alias')">
                <button
                    @click.prevent="changeOrder('alias')"
                    :title="orderDir === 'asc'
                            ? $gettext('Sortiere absteigend nach Kürzel')
                            : $gettext('Sortiere aufsteigend nach Kürzel')"
                    class="as-link"
                >
                    {{ $gettext('Kürzel') }}
                </button>
            </th>
            <th :class="getOrderClasses('title')">
                <button
                    @click.prevent="changeOrder('title')"
                    :title="orderDir === 'asc'
                            ? $gettext('Sortiere absteigend nach Titel der Zielseite')
                            : $gettext('Sortiere aufsteigend nach Titel der Zielseite')"
                    class="as-link"
                >
                    {{ $gettext('Titel der Zielseite') }}
                </button>
            </th>
            <th :class="getOrderClasses('chdate')">
                <button
                    @click.prevent="changeOrder('chdate')"
                    :title="orderDir === 'asc'
                            ? $gettext('Sortiere absteigend nach Änderungsdatum')
                            : $gettext('Sortiere aufsteigend nach Änderungsdatum')"
                    class="as-link"
                >
                    {{ $gettext('Erstellt/Geändert') }}
                </button>
            </th>
            <th>{{ $gettext('Aktionen') }}</th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="(shortUrl, index) in sortEntries(store.getShortUrls())" :key="index">
            <td>
                <button class="as-link copy-link"
                        :title="$gettext('In die Zwischenablage kopieren')"
                        @click.prevent="copyToClipboard(shortUrl.attributes.alias)">
                    <studip-icon shape="clipboard" />
                </button>
                <a :href="store.getShortUrl(shortUrl.attributes.alias)" :title="$gettext('Titel des Kurzlinks')">
                    {{ shortUrl.attributes.alias }}
                </a>
            </td>
            <td>
                {{ shortUrl.attributes.title }}
            </td>
            <td>
                {{ formatDate(shortUrl.attributes.chdate) }}
            </td>
            <td class="actions">
                <studip-action-menu
                    :items="actionMenuItems"
                    @qrcode="createQrCode(shortUrl)"
                    @edit="editShortUrl(shortUrl)"
                    @delete="store.deleteShortUrl(shortUrl.id)"
                    @copy="copyToClipboard(shortUrl.attributes.alias)"
                />
            </td>
        </tr>
        </tbody>
    </table>

    <studip-message-box v-else type="info">
        {{ $gettext('Es wurden keine Kurzlinks gefunden.') }}
    </studip-message-box>

    <div id="qrcode"
         v-if="qrUrl"
         ref="qrcode"
    >
        <qrcode-svg
            :value="qrUrl"
            :size="600"></qrcode-svg>
    </div>

    <studip-dialog
        v-if="currentlyEditing !== null"
        :title="$gettext('Kurzlink bearbeiten')"
        :confirmText="$gettext('Aktualisieren')"
        confirmClass="accept"
        :closeText="$gettext('Schließen')"
        closeClass="cancel"
        :height="420"
        @close="closeEditDialog"
        @confirm="save"
    >
        <template #dialogContent>
            <shortUrl :shortLink="currentlyEditing"/>
        </template>
    </studip-dialog>
</template>

<script setup>
import {ref, onMounted, nextTick} from 'vue';
import {useShortUrlsStore} from '../../store/pinia/shortUrlsStore';
import {$gettext} from "../../../assets/javascripts/lib/gettext";
import StudipProgressIndicator from '../../components/StudipProgressIndicator.vue';
import StudipDialog from '../../components/StudipDialog';
import StudipActionMenu from '../../components/StudipActionMenu';
import ShortUrl from './ShortUrl';
import QrcodeSvg from 'qrcode.vue';

const loading = ref(true);
const store = useShortUrlsStore();
const qrUrl = ref(null);
const qrcode = ref(null);
const orderBy = ref('chdate');
const orderDir = ref('desc');
const currentlyEditing = ref(null);
const actionMenuItems = [
    {label: $gettext('In die Zwischenablage kopieren'), icon: 'clipboard', emit: 'copy'},
    {label: $gettext('QR-Code herunterladen'), icon: 'code-qr', emit: 'qrcode'},
    {label: $gettext('Bearbeiten'), icon: 'edit', emit: 'edit'},
    {label: $gettext('Löschen'), icon: 'trash', emit: 'delete'}
];

const formatDate = (datestring) => {
    const date = new Date(datestring);
    const formatter = new Intl.DateTimeFormat(String.locale, {
        dateStyle: 'short',
        timeStyle: 'short'
    });

    // We need to get rid of the comma separating date and time
    const parts = formatter.formatToParts(date);
    return parts.filter(p => p.type !== 'literal')
        .map(p => p.value)
        .join(' ');
}

const getAliasLink = (alias) => {
    return STUDIP.URLHelper.getURL('dispatch.php/u/r/' + alias,  {}, true);
}

const copyToClipboard = (alias) => {
    const shortUrl = getAliasLink(alias);
    navigator.clipboard.writeText(shortUrl);
    STUDIP.Report.success($gettext('Sie finden den Link in der Zwischenablage'));
}

/*
 * Create a QR code and trigger download as png.
 */
const createQrCode = (shortLink) => {
    qrUrl.value = getAliasLink(shortLink.attributes.alias);
    nextTick().then(() => {
        const png = qrcode.value.querySelector('canvas').toDataURL('image/png');
        const link = document.createElement('a');
        link.download = 'shortlink-' + shortLink.attributes.alias + '.png';
        link.href = png;
        link.click();
        qrUrl.value = null;
    });
}

const editShortUrl = (url) => {
    currentlyEditing.value = url;
}

const closeEditDialog = () => {
    currentlyEditing.value = null;
}

const save = async () => {
    await store.storeShortUrl(currentlyEditing.value);
    closeEditDialog();
}

const sortEntries = (entries) => {
    const sorted = [...entries].toSorted((a, b) => {
        return orderDir.value === 'asc'
            ? a.attributes[orderBy.value].localeCompare(b.attributes[orderBy.value])
            : b.attributes[orderBy.value].localeCompare(a.attributes[orderBy.value]);
    });
    return sorted;
}

const getOrderClasses = (by) => {
    if (by !== orderBy.value) {
        return [];
    }
    return orderDir.value === 'asc' ? ['sortasc'] : ['sortdesc'];
}

const changeOrder = (by) => {
    if (orderBy.value === by) {
        orderDir.value = orderDir.value === 'asc' ? 'desc' : 'asc';
    } else {
        orderBy.value = by;
        orderDir.value = 'asc';
    }
}

onMounted(() => {
    store.initialize().then(() => {
        loading.value = false;
    });
});
</script>

<style scoped>
.copy-link {
    vertical-align: middle;
}
#qrcode {
    display: none;
    visibility: hidden;
}
</style>
