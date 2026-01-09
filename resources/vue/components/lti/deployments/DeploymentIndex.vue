<script setup>
import {$gettext} from "../../../../assets/javascripts/lib/gettext";
import {useSortable} from "../../../composables/useSortable";
import {ref} from "vue";
import StudipActionMenu from "../../../components/StudipActionMenu.vue";
import StudipDateTime from "../../../components/StudipDateTime.vue";
import StudipIcon from "../../StudipIcon.vue";
import {addDeploymentURL, deleteDeploymentURL, editDeploymentURL, showRangeURL} from "../helpers/urls";
import CopyableCodeBlock from "../../CopyableCodeBlock.vue";

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

const getActionMenus = deployment => {
    const base = [
        { label: $gettext('Bearbeiten'),  icon: 'edit', emit: 'edit'}
    ];

    if (!deployment.is_default) {
        return [
            ...base,
            { label: $gettext('Löschen'),  icon: 'trash', emit: 'delete'}
        ]
    }

    return base;
};

const showConfirmDelete = (id, name) => STUDIP.Dialog.confirm(
    $gettext('Wollen Sie diese "%{name}" Registrierung löschen?', {name}),
    () => deleteDeployment(id),
    STUDIP.Dialog.close()
);

const deleteDeployment = id => {
    const deleteForm = document.getElementById('lti-deployment-delete-form');
    deleteForm.action = deleteDeploymentURL(id);
    deleteForm.submit();
}

const addDeployment = () => STUDIP.Dialog.fromURL(addDeploymentURL(props.registration.id), { width: '500', height: '400'});

const editDeployment = id => STUDIP.Dialog.fromURL(editDeploymentURL(id), { width: '500', height: '400'});
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
                    :class="getSortClass('deployment_key')"
                    :aria-sort="getAriaSortString('deployment_key')"
                    :aria-label="getAriaSortLabel('deployment_key', $gettext('Deployment-ID'))"
                >
                    <button
                        type="button"
                        class="button__table-sort button-base"
                        @click="sortBy('deployment_key')"
                        :title="$gettext('Nach Deployment-ID sortieren')">
                        {{ $gettext('Deployment-ID') }}
                    </button>
                </th>
                <th
                    scope="col"
                    :class="getSortClass('client_id')"
                    :aria-sort="getAriaSortString('client_id')"
                    :aria-label="getAriaSortLabel('client_id', $gettext('Client-ID'))"
                >
                    <button
                        type="button"
                        class="button__table-sort button-base"
                        @click="sortBy('client_id')"
                        :title="$gettext('Nach Client-ID sortieren')">
                        {{ $gettext('Client-ID') }}
                    </button>
                </th>
                <th
                    scope="col"
                    :class="getSortClass('purpose')"
                    :aria-sort="getAriaSortString('purpose')"
                    :aria-label="getAriaSortLabel('purpose', $gettext('Zweck'))"
                >
                    <button
                        type="button"
                        class="button__table-sort button-base"
                        @click="sortBy('purpose')"
                        :title="$gettext('Nach Zweck sortieren')">
                        {{ $gettext('Zweck') }}
                    </button>
                </th>
                <th
                    scope="col"
                    v-if="registration.role === 'tool'"
                    :class="getSortClass('resource_name')"
                    :aria-sort="getAriaSortString('resource_name')"
                    :aria-label="getAriaSortLabel('resource_name', $gettext('Bereich'))"
                >
                    <button
                        type="button"
                        class="button__table-sort button-base"
                        @click="sortBy('resource_name')"
                        :title="$gettext('Nach Name des Bereich sortieren')">
                        {{ $gettext('Bereich') }}
                    </button>
                </th>
                <th
                    scope="col"
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
                <th scope="col" class="actions" style="width: 20px">{{ $gettext('Aktionen') }}</th>
            </tr>
        </thead>

        <tbody>
        <tr v-for="deployment in sortedDeployments" :key="deployment.id">
            <td>{{ deployment.name }}</td>
            <td>
                <div style="width: 150px;">
                    <CopyableCodeBlock :content="deployment.deployment_key" />
                </div>
            </td>
            <td>
                <div style="width: 350px;">
                    <CopyableCodeBlock :content="deployment.client_id" />
                </div>
            </td>
            <td>{{ deployment.purpose }}</td>
            <td v-if="registration.role === 'tool'">
                <a v-if="deployment.resource_id !== 'global'" :href="showRangeURL(deployment.resource_id)" :title="$gettext('Zur Veranstaltung')">
                    {{ deployment.resource_name }}
                </a>
                <template v-else>
                    {{ deployment.resource_name }}
                </template>
            </td>
            <td>
                <StudipDateTime :iso="deployment.mkdate" :relative="true" />
            </td>
            <td class="actions">
                <StudipActionMenu
                    :context="deployment.name"
                    :items="getActionMenus(deployment)"
                    @edit="editDeployment(deployment.id)"
                    @delete="showConfirmDelete(deployment.id, deployment.name)"
                />
            </td>
        </tr>

        <tr v-if="deployments.length === 0">
            <td colspan="7">
                {{ $gettext('Keine Deployments vorhanden.') }}
            </td>
        </tr>
        </tbody>
    </table>

    <form id="lti-deployment-delete-form" method="post">
        <input type="hidden" :name="CSRF.name" :value="CSRF.value" />
    </form>
</template>

<style lang="scss">
.copyable-code-block {
    margin: 0;
}
</style>
