<script setup>
import {$gettext} from "../../../../assets/javascripts/lib/gettext";
import {useSortable} from "../../../composables/useSortable";
import {computed, ref} from "vue";
import StudipActionMenu from "../../../components/StudipActionMenu.vue";
import StudipDateTime from "../../../components/StudipDateTime.vue";
import StudipIcon from "../../StudipIcon.vue";

const CSRF = STUDIP.CSRF_TOKEN;
const props = defineProps({
    deployments: {
        type: Array,
        default: () => ([])
    },
    registration: {
        type: Object,
        required: true
    },
    withCaption: {
        type: Boolean,
        default: false
    }
});

const deploymentsRef = ref(props.deployments);

const {
    sortedData: sortedDeployments,
    sortBy,
    getSortClass,
    getAriaSortString,
    getAriaSortLabel
} = useSortable(deploymentsRef);

const actionMenus = computed(() => {
    return [
        { label: $gettext('Löschen'),  icon: 'trash', emit: 'delete'}
    ];
});

const showConfirmDelete = (id, name) => STUDIP.Dialog.confirm(
    $gettext('Wollen Sie diese "%{name}" Registrierung löschen?', {name}),
    () => deleteDeployment(id),
    STUDIP.Dialog.close()
);

const deleteDeployment = id => {
    const deleteForm = document.getElementById('lti-deployment-delete-form');
    deleteForm.action = STUDIP.URLHelper.getURL(`dispatch.php/admin/lti/deployments/delete/${id}`);
    deleteForm.submit();
}

const addDeployment = () => STUDIP.Dialog.fromURL(STUDIP.URLHelper.getURL(`dispatch.php/admin/lti/deployments/create?registration_id=${props.registration.id}`), { width: '500', height: '400'});
</script>

<template>
    <table class="default">
        <caption v-if="withCaption">
            {{ $gettext('LTI-Deploymetns') }}

            <div class="actions">
                <button
                    type="button"
                    class="button button--icon-only"
                    @click="addDeployment"
                    :title="$gettext('Neues Deployment anlegen')">
                    <StudipIcon shape="add" aria-hidden="true" />
                </button>
            </div>
        </caption>
        <thead>
            <tr class="sortable">
            <th
                :class="getSortClass('name')"
                :aria-sort="getAriaSortString('name')"
                :aria-label="getAriaSortLabel('name', $gettext('Name'))"
            >
                <a
                    href="#"
                    @click.prevent="sortBy('name')"
                    :title="$gettext('Nach Name sortieren')">
                    {{ $gettext('Name') }}
                </a>
            </th>
            <th
                :class="getSortClass('purpose')"
                :aria-sort="getAriaSortString('purpose')"
                :aria-label="getAriaSortLabel('purpose', $gettext('Zweck'))"
            >
                <a
                    href="#"
                    @click.prevent="sortBy('purpose')"
                    :title="$gettext('Nach Zweck sortieren')">
                    {{ $gettext('Zweck') }}
                </a>
            </th>
            <th
                :class="getSortClass('deployment_id')"
                :aria-sort="getAriaSortString('deployment_id')"
                :aria-label="getAriaSortLabel('deployment_id', $gettext('Deployment-ID'))"
            >
                <a
                    href="#"
                    @click.prevent="sortBy('deployment_id')"
                    :title="$gettext('Nach Deployment-ID sortieren')">
                    {{ $gettext('Deployment-ID') }}
                </a>
            </th>
            <th
                :class="getSortClass('mkdate')"
                :aria-sort="getAriaSortString('mkdate')"
                :aria-label="getAriaSortLabel('mkdate', $gettext('Erstellt am'))"
            >
                <a
                    href="#"
                    @click.prevent="sortBy('mkdate')"
                    :title="$gettext('Nach Erstellt Datum sortieren')">
                    {{ $gettext('Erstellt am') }}
                </a>
            </th>
            <th class="actions">{{ $gettext('Aktionen') }}</th>
        </tr>
        </thead>

        <tbody>
        <tr v-for="deployment in sortedDeployments" :key="deployment.id">
            <td>
                {{ deployment.name }}
            </td>
            <td>{{ deployment.purpose }}</td>
            <td>{{ deployment.deployment_id }}</td>
            <td>
                <StudipDateTime :iso="deployment.mkdate" :relative="true" />
            </td>

            <td class="actions">
                <StudipActionMenu
                    :items="actionMenus"
                    @delete="showConfirmDelete(deployment.id, deployment.name)"
                />
            </td>
        </tr>

        <tr v-if="deployments.length === 0">
            <td colspan="5">
                {{ $gettext('Keine Deployments vorhanden.') }}
            </td>
        </tr>
        </tbody>
    </table>

    <form id="lti-deployment-delete-form" method="post">
        <input type="hidden" :name="CSRF.name" :value="CSRF.value">
    </form>
</template>
