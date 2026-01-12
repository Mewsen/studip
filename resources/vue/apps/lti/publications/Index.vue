<script setup>
import {$gettext} from "../../../../assets/javascripts/lib/gettext";
import {useSortable} from "../../../composables/useSortable";
import {computed, ref} from "vue";
import StudipActionMenu from "../../../components/StudipActionMenu.vue";
import StudipDateTime from "../../../components/StudipDateTime.vue";
import StudipIcon from "../../../components/StudipIcon.vue";
import {
    createPublicationURL,
    deletePublicationURL,
    editPublicationURL,
    showPublicationURL,
    showRangeURL, userProfileURL,
} from "../../../components/lti/helpers/urls";
import LtiApp from "../../../components/lti/LtiApp.vue";
import CopyableCodeBlock from "../../../components/CopyableCodeBlock.vue";
import UserAvatarDropdown from "../../../components/avatar/UserAvatarDropdown.vue";

const CSRF = STUDIP.CSRF_TOKEN;
const RANGE_ID = STUDIP.URLHelper.parameters.cid;

const props = defineProps({
    publications: {
        type: Array,
        default: () => ([])
    }
});

const publicationsRef = ref(props.publications);

const {
    sortedData: sortedPublications,
    sortBy,
    getSortClass,
    getAriaSortString,
    getAriaSortLabel
} = useSortable(publicationsRef);

const actionMenus = computed(() => {
    return [
        { label: $gettext('Konfiguration anzeigen'),  icon: 'info', emit: 'show'},
        { label: $gettext('Bearbeiten'),  icon: 'edit', emit: 'edit'},
        { label: $gettext('Löschen'),  icon: 'trash', emit: 'delete'}
    ];
});


const showPublication = id => STUDIP.Dialog.fromURL(showPublicationURL(id), { width: '700' });
const addPublication = () => STUDIP.Dialog.fromURL(createPublicationURL(), { width: '700' });

const editPublication = id => STUDIP.Dialog.fromURL(editPublicationURL(id), { width: '700' });
const showConfirmDelete = (id, name) => STUDIP.Dialog.confirm(
    $gettext('Wollen Sie diese "%{name}" Veröffentlichung löschen?', {name}),
    () => deletePublication(id),
    STUDIP.Dialog.close()
);

const deletePublication = id => {
    const deleteForm = document.getElementById('lti-publication-delete-form');
    deleteForm.action = deletePublicationURL(id);
    deleteForm.submit();
}
</script>


<template>
    <LtiApp>
        <header class="header">
            <div class="header__content header__content--with-actions">
                <ul class="breadcrumb">
                    <li>
                        {{ $gettext('LTI-Veröffentlichungen') }}
                    </li>
                </ul>

                <div class="actions">
                    <button
                        type="button"
                        class="button button--icon-only"
                        @click="addPublication"
                        :title="$gettext('Neue Veröffentlichung anlegen')">
                        <StudipIcon shape="add" aria-hidden="true" />
                    </button>
                </div>
            </div>
        </header>

        <table class="default">
            <thead>
                <tr class="sortable">
                    <th
                        :class="getSortClass('name')"
                        :aria-sort="getAriaSortString('name')"
                        :aria-label="getAriaSortLabel('name', $gettext('Name'))"
                    >
                        <button
                            type="button"
                            class="button__table-sort button-base"
                            @click="sortBy('name')"
                            :title="$gettext('Nach Name sortieren')">
                            {{ $gettext('Name') }}
                        </button>
                    </th>
                    <th
                        :class="getSortClass('version')"
                        :aria-sort="getAriaSortString('version')"
                        :aria-label="getAriaSortLabel('version', $gettext('Version'))"
                    >
                        <button
                            type="button"
                            class="button__table-sort button-base"
                            @click="sortBy('version')"
                            :title="$gettext('Nach Version sortieren')">
                            {{ $gettext('Version') }}
                        </button>
                    </th>
                    <th
                        v-if="!RANGE_ID"
                        :class="getSortClass('range_name')"
                        :aria-sort="getAriaSortString('range_name')"
                        :aria-label="getAriaSortLabel('range_name', $gettext('Bereich'))"
                    >
                        <button
                            type="button"
                            class="button__table-sort button-base"
                            @click="sortBy('range_name')"
                            :title="$gettext('Nach Bereich sortieren')">
                            {{ $gettext('Bereich') }}
                        </button>
                    </th>
                    <th
                        :class="getSortClass('status.value')"
                        :aria-sort="getAriaSortString('status.value')"
                        :aria-label="getAriaSortLabel('status.value', $gettext('Status'))"
                    >
                        <button
                            type="button"
                            class="button__table-sort button-base"
                            @click="sortBy('status.value')"
                            :title="$gettext('Nach Status sortieren')">
                            {{ $gettext('Status') }}
                        </button>
                    </th>
                    <th
                        :class="getSortClass('members')"
                        :aria-sort="getAriaSortString('members')"
                        :aria-label="getAriaSortLabel('members', $gettext('Anzahl der Teilnehmenden'))"
                    >
                        <button
                            type="button"
                            class="button__table-sort button-base"
                            @click="sortBy('members')"
                            :title="$gettext('Nach Anzahl der Teilnehmenden sortieren')">
                            {{ $gettext('Anzahl der Teilnehmenden') }}
                        </button>
                    </th>
                    <th>
                        {{ $gettext('Custom-Parameter') }}
                    </th>
                    <th
                        :class="getSortClass('user.name')"
                        :aria-sort="getAriaSortString('user.name')"
                        :aria-label="getAriaSortLabel('user.name', $gettext('Erstellt von'))"
                    >
                        <button
                            type="button"
                            class="button__table-sort button-base"
                            @click="sortBy('user.name')"
                            :title="$gettext('Nach Autor sortieren')">
                            {{ $gettext('Erstellt von') }}
                        </button>
                    </th>
                    <th
                        :class="getSortClass('mkdate')"
                        :aria-sort="getAriaSortString('mkdate')"
                        :aria-label="getAriaSortLabel('mkdate', $gettext('Erstellt am'))"
                    >
                        <button
                            type="button"
                            class="button__table-sort button-base"
                            @click="sortBy('mkdate')"
                            :title="$gettext('Nach Erstellt Datum sortieren')">
                            {{ $gettext('Erstellt am') }}
                        </button>
                    </th>
                    <th class="actions" style="width: 20px">{{ $gettext('Aktionen') }}</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="publication in sortedPublications" :key="publication.id">
                    <td>
                        <button
                            type="button"
                            class="styleless button-base"
                            @click="showPublication(publication.id)"
                            :title="$gettext('Konfiguration anzeigen')">
                            {{ publication.name }}
                        </button>
                    </td>
                    <td>{{ publication.version }}</td>
                    <td v-if="!RANGE_ID">
                        <a :href="showRangeURL(publication.range_id)" :title="$gettext('Zur Veranstaltung')">
                            {{ publication.range_name }}
                        </a>
                    </td>
                    <td>
                        <span class="status-label"
                            :class="{
                                'status-label--success': publication.status.value === 'active',
                                'status-label--warning': publication.status.value === 'inactive'
                            }"
                        >
                            {{ publication.status.label }}
                        </span>
                    </td>
                    <td>
                        {{ publication.members.length }}
                    </td>
                    <td>
                        <div style="width: 400px">
                            <CopyableCodeBlock :content="publication.custom_parameter" />
                        </div>
                    </td>
                    <td>
                        <div class="user-avatar-container">
                            <UserAvatarDropdown :user="publication.user" />
                            <a :href="userProfileURL(publication.user.username)" :title="$gettext('Zum Benutzerprofil')">
                                {{ publication.user.name }}
                            </a>
                        </div>
                    </td>
                    <td>
                        <StudipDateTime :iso="publication.mkdate" :relative="true" />
                    </td>
                    <td class="actions">
                        <StudipActionMenu
                            :context="publication.name"
                            :items="actionMenus"
                            @show="showPublication(publication.id)"
                            @edit="editPublication(publication.id)"
                            @delete="showConfirmDelete(publication.id, publication.name)"
                        />
                    </td>
                </tr>
                <tr v-if="sortedPublications.length === 0">
                    <td colspan="7">
                        {{ $gettext('Keine LTI-Veröffentlichungen vorhanden.') }}
                    </td>
                </tr>
            </tbody>
        </table>

        <form id="lti-publication-delete-form" method="post">
            <input type="hidden" :name="CSRF.name" :value="CSRF.value" />
        </form>
    </LtiApp>
</template>

<style lang="scss">
.copyable-code-block {
    margin: 0;
}
</style>

