<script setup>
import {$gettext} from "../../../../assets/javascripts/lib/gettext";
import {useSortable} from "../../../composables/useSortable";
import {computed, ref} from "vue";
import StudipActionMenu from "../../../components/StudipActionMenu.vue";
import StudipDateTime from "../../../components/StudipDateTime.vue";
import StudipIcon from "../../../components/StudipIcon.vue";
import {
    addDeploymentURL,
    createRegistrationURL,
    deleteRegistrationURL,
    editRegistrationURL,
    showRangeURL,
    showRegistrationURL
} from "../../../components/lti/helpers/urls";
import LtiApp from "../../../components/lti/LtiApp.vue";

const CSRF = STUDIP.CSRF_TOKEN;

const props = defineProps({
    registrations: {
        type: Array,
        default: () => ([])
    },
    role: {
        type: String,
        default: 'tool'
    }
});

const registrationsRef = ref(props.registrations);

const {
    sortedData: sortedRegistrations,
    sortBy,
    getSortClass,
    getAriaSortString,
    getAriaSortLabel
} = useSortable(registrationsRef);

const actionMenus = computed(() => {
    return [
        { label: $gettext('Neues Deployment anlegen'),  icon: 'add', emit: 'addDeployment'},
        { label: $gettext('Bearbeiten'),  icon: 'edit', emit: 'edit'},
        { label: $gettext('Löschen'),  icon: 'trash', emit: 'delete'}
    ];
});

const pageTitle = computed(() => {
    if (props.role === 'tool') {
        return $gettext('LTI-Tools');
    } else if (props.role === 'platform') {
        return $gettext('LTI-Platforms');
    }

    return $gettext('LTI-Registrierungen');
});

const addRegistration = () => STUDIP.Dialog.fromURL(createRegistrationURL(props.role), { width: '900' });

const addDeployment = registrationId => STUDIP.Dialog.fromURL(addDeploymentURL(registrationId), { width: '500', height: '400'});

const editRegistration = id => STUDIP.Dialog.fromURL(editRegistrationURL(id, props.role), { width: '900' });
const getDeploymentsURL = id => STUDIP.URLHelper.getURL(`dispatch.php/admin/lti/deployments?registration_id=${id}&role=${props.role}`);
const showConfirmDelete = (id, name) => STUDIP.Dialog.confirm(
    $gettext('Wollen Sie diese "%{name}" Registrierung löschen?', {name}),
    () => deleteRegistration(id),
    STUDIP.Dialog.close()
);

const deleteRegistration = id => {
    const deleteForm = document.getElementById('lti-registration-delete-form');
    deleteForm.action = deleteRegistrationURL(id);
    deleteForm.submit();
}
</script>


<template>
    <LtiApp>
        <header class="header">
            <div class="header__content header__content--with-actions">
                <ul class="breadcrumb">
                    <li>
                        {{ pageTitle }}
                    </li>
                </ul>

                <div class="actions">
                    <button
                        type="button"
                        class="button button--icon-only"
                        @click="addRegistration"
                        :title="role === 'platform' ? $gettext('Neues LTI-Platform registrieren') : $gettext('Neues LTI-Tool registrieren')">
                        <StudipIcon shape="add" aria-hidden="true" />
                    </button>
                </div>
            </div>
        </header>

        <table class="default">
            <thead>
                <tr class="sortable">
                    <th
                        scope="col"
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
                        scope="col"
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
                        scope="col"
                        :class="getSortClass('deployments')"
                        :aria-sort="getAriaSortString('deployments')"
                        :aria-label="getAriaSortLabel('deployments', $gettext('Anzahl der Deployments'))"
                    >
                        <button
                            type="button"
                            class="button__table-sort button-base"
                            @click="sortBy('deployments')"
                            :title="$gettext('Nach Anzahl der Deployments sortieren')">
                            {{ $gettext('Deployments') }}
                        </button>
                    </th>
                    <th
                        scope="col"
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
                        scope="col"
                        :class="getSortClass('state')"
                        :aria-sort="getAriaSortString('state')"
                        :aria-label="getAriaSortLabel('state', $gettext('Status'))"
                    >
                        <button
                            type="button"
                            class="button__table-sort button-base"
                            @click="sortBy('state')"
                            :title="$gettext('Nach Status sortieren')">
                            {{ $gettext('Status') }}
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
                <tr v-for="registration in sortedRegistrations" :key="registration.id">
                    <td>
                        <a
                            :href="showRegistrationURL(registration.id, registration.role)"
                            :title="$gettext('Registrierung anschauen')">
                            {{ registration.name }}
                        </a>
                    </td>
                    <td>{{ registration.version }}</td>
                    <td>
                        <a v-if="registration.version === '1.3a'" :href="getDeploymentsURL(registration.id)">
                            {{ registration.deployments.length }}
                        </a>
                    </td>
                    <td>
                        <a v-if="registration.range_id !== 'global'" :href="showRangeURL(registration.range_id)" :title="$gettext('Zur Veranstaltung')">
                            {{ registration.range_name }}
                        </a>
                        <template v-else>
                            {{ registration.range_name }}
                        </template>
                    </td>
                    <td>
                        <span class="status-label"
                              :class="{
                                'status-label--success': registration.state,
                                'status-label--warning': !registration.state
                              }"
                        >
                            {{ registration.state ? $gettext('Aktiv') : $gettext('Ausstehend') }}
                        </span>
                    </td>

                    <td>
                        <StudipDateTime :iso="registration.mkdate" :relative="true" />
                    </td>

                    <td class="actions">
                        <StudipActionMenu
                            :context="registration.name"
                            :items="actionMenus"
                            @edit="editRegistration(registration.id)"
                            @addDeployment="addDeployment(registration.id)"
                            @delete="showConfirmDelete(registration.id, registration.name)"
                        />
                    </td>
                </tr>
                <tr v-if="registrations.length === 0">
                    <td colspan="7">
                        {{ $gettext('Keine LTI-Registrierungen vorhanden.') }}
                    </td>
                </tr>
            </tbody>
        </table>

        <form id="lti-registration-delete-form" method="post">
            <input type="hidden" :name="CSRF.name" :value="CSRF.value" />
        </form>
    </LtiApp>
</template>
