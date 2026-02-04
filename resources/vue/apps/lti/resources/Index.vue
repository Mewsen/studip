<script setup>
import {$gettext} from '../../../../assets/javascripts/lib/gettext';
import StudipIcon from '../../../components/StudipIcon.vue';
import draggable from 'vuedraggable';
import {nextTick, onMounted, ref} from 'vue';
import ToolCard from '../../../components/lti/resources/ToolCard.vue';
import {createResourceURL} from '../../../components/lti/helpers/urls';
import LtiApp from '../../../components/lti/LtiApp.vue';
import {useLtiConfig} from '../../../store/pinia/lti/Config';
import {debounce} from 'lodash';
import {deserializeJSONAPIResponse} from "../../../../assets/javascripts/lib/jsonapiUtils";
import StudipPagination from "../../../components/StudipPagination.vue";
import ResourceDetail from "../../../components/lti/resources/ResourceDetail.vue";
import StudipDialog from "../../../components/StudipDialog.vue";

const ltiConfig = useLtiConfig();

const CSRF = STUDIP.CSRF_TOKEN;

const resources = ref([]);
const currentResource = ref(null);
const isLoading = ref(false);
const pagination = ref({});

const createResource = () => STUDIP.Dialog.fromURL(createResourceURL(), {width: '700', height: '750'});

const showResourceDialog = resource => currentResource.value = resource;

const isIframe = resource => {
    const launchContainer = resource.launch_container || resource.registration.meta.configs.launch_container;
    return launchContainer === 'iframe' && resource.registration.status !== 'inactive';
};
const updateResourcesOrder = async () => {
    try {
        const resourceIds = resources.value.map(({ id }) => id);

        const data = {
            attributes: {
                'resource-ids': resourceIds
            },
            relationships: {
                range: {
                    data: {
                        type: 'courses',
                        id: STUDIP.URLHelper.parameters.cid
                    }
                }
            }
        };

        await STUDIP.jsonapi.withPromises().PATCH(
            `lti-resources/sort`,
            { data: { data } }
        );
    } catch (error) {
        STUDIP.Report.error(error);
    }
}

const updateOrderDebounced = debounce(updateResourcesOrder, 2000);
const assistiveLive = ref('');

const swapResource = (resourceId, step) => {
    const index = resources.value.findIndex(({ id }) => id === resourceId);
    const newIndex = index + step;

    if (newIndex < 0 || newIndex >= resources.value.length) {
        return;
    }

    const temp = resources.value[newIndex];
    resources.value[newIndex] = resources.value[index];
    resources.value[index] = temp;

    nextTick(() => {
        document.getElementById(`sort-handle-${resourceId}`)?.focus();
        assistiveLive.value = $gettext(
            'Aktuelle Position in der Liste: %{index} von %{length}.',
            { index: newIndex + 1, length: resources.value.length }
        );

        updateOrderDebounced();
    });
}

const fetchResources = async (_, offset = 0) => {
    try {
        isLoading.value = true;

        const response = await STUDIP.jsonapi.withPromises().GET(
            `courses/${STUDIP.URLHelper.parameters.cid}/lti-resources`,
            {
                data: { include: 'registration,deployment', page: { offset } }
            }
        );

        pagination.value = {
            ...response.meta.page,
            currentPage: response.meta.page.offset / response.meta.page.limit,
            links: response.links
        };

        resources.value = await deserializeJSONAPIResponse(response);

        console.log(resources.value);
    } catch (error) {
        STUDIP.Report.error(error);
    } finally {
        isLoading.value = false;
    }
}

onMounted(async () => {
    await fetchResources();
});
</script>


<template>
    <LtiApp>
        <header class="header">
            <div class="header__content header__content--with-actions">
                <h2>
                    {{ $gettext('LTI-Ressourcen') }}
                </h2>

                <div v-if="ltiConfig.isModerator" class="actions">
                    <button
                        type="button"
                        class="button button--icon-only"
                        @click="createResource"
                        :title="$gettext('Neuen Ressource hinzufügen')">
                        <StudipIcon shape="add" aria-hidden="true" />
                    </button>
                </div>
            </div>
        </header>

        <span aria-live="assertive" class="sr-only">{{ assistiveLive }}</span>

        <draggable
            v-model="resources"
            item-key="id"
            :animation="200"
            @end="updateResourcesOrder"
            :disabled="false"
            class="tools-card-container"
            :class="{ 'tools-card-container--fill-free-space': resources.length >= 1 }"
            handle=".drag-handle"
            tag="ul">
            <template #item="{element}">
                <li :class="{ 'tools-card-container--full-width': isIframe(element) }">
                    <ToolCard
                        :resource="element"
                        @swap="swapResource"
                        @showResource="showResourceDialog(element)"
                    />
                </li>
            </template>
            <template v-if="ltiConfig.isModerator" #footer>
                <li key="footer">
                    <div class="tool-card tool-card--create">
                        <div class="studip-card">
                            <button
                                type="button"
                                @click="createResource"
                                class="button button--icon-label"
                            >
                                <StudipIcon shape="add" :size="20" aria-hidden="true" />
                                <span class="label">{{ $gettext('Neuen Ressource hinzufügen') }}</span>
                            </button>
                        </div>
                    </div>
                </li>
            </template>
        </draggable>
        <StudipPagination
            v-if="pagination.total > pagination.limit"
            :currentPage="pagination.currentPage"
            :totalItems="pagination.total"
            :itemsPerPage="pagination.limit"
            @pageUpdated="fetchResources" />

        <StudipDialog
            v-if="currentResource?.id"
            :title="$gettext('Detaillierte Information')"
            :closeText="$gettext('Schließen')"
            height="700"
            width="600"
            @close="currentResource = null"
        >
            <template #dialogContent>
                <div class="lti">
                    <ResourceDetail :resource="currentResource" />
                </div>
            </template>
        </StudipDialog>

        <form id="lti-resource-delete-form" method="post">
            <input type="hidden" :name="CSRF.name" :value="CSRF.value" />
        </form>
    </LtiApp>
</template>
