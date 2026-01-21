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

const ltiConfig = useLtiConfig();

const CSRF = STUDIP.CSRF_TOKEN;

const props = defineProps({
    resources: {
        type: Array,
        default: () => ([])
    }
});

const filteredResources = ref(props.resources);

const createResource = () => STUDIP.Dialog.fromURL(createResourceURL(), {width: '700', height: '700'});

const isIframe = resource => resource.container.value === 'iframe' || resource.registration.container.value === 'iframe';
const updateResourcesOrder = async () => {
    try {
        const resourceIds = filteredResources.value.map(({ id }) => id);

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
    const index = filteredResources.value.findIndex(({ id }) => id === resourceId);
    const newIndex = index + step;

    if (newIndex < 0 || newIndex >= filteredResources.value.length) {
        return;
    }

    const temp = filteredResources.value[newIndex];
    filteredResources.value[newIndex] = filteredResources.value[index];
    filteredResources.value[index] = temp;

    nextTick(() => {
        document.getElementById(`sort-handle-${resourceId}`)?.focus();
        assistiveLive.value = $gettext(
            'Aktuelle Position in der Liste: %{index} von %{length}.',
            { index: newIndex + 1, length: filteredResources.value.length }
        );

        updateOrderDebounced();
    });
}

onMounted(() => {
    // filteredResources.value = props.resources.filter(r => ltiConfig.isModerator || r.launch_type !== 'deep_linking');
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
            v-model="filteredResources"
            item-key="id"
            :animation="200"
            @end="updateResourcesOrder"
            :disabled="false"
            class="tools-card-container"
            :class="{ 'tools-card-container--fill-free-space': filteredResources.length >= 1 }"
            handle=".drag-handle"
            tag="ul">
            <template #item="{element}">
                <li :class="{ 'tools-card-container--full-width': isIframe(element) }">
                    <ToolCard :resource="element" @swap="swapResource" />
                </li>
            </template>
            <template v-if="ltiConfig.isModerator" #footer>
                <li key="footer">
                    <div class="tool-card tool-card--create">
                        <div class="studip-card">
                            <button
                                type="button"
                                @click="createResource"
                                :title="$gettext('Neuen Ressource hinzufügen')"
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

        <form id="lti-resource-delete-form" method="post">
            <input type="hidden" :name="CSRF.name" :value="CSRF.value" />
        </form>
    </LtiApp>
</template>
