<script setup>
import {$gettext} from "../../../../assets/javascripts/lib/gettext";
import StudipIcon from "../../../components/StudipIcon.vue";
import draggable from "vuedraggable";
import {ref} from "vue";
import ToolCard from "../../../components/lti/resources/ToolCard.vue";
import {createResourceURL} from "../../../components/lti/helpers/urls";
import {getCategoryCreateURL} from "../../../components/forum/helpers/urls";

const CSRF = STUDIP.CSRF_TOKEN;

const props = defineProps({
    resources: {
        type: Array,
        default: () => ([])
    }
});

const resourcesRef = ref(props.resources);

const createResource = () => STUDIP.Dialog.fromURL(createResourceURL(), {width: '700', height: '700'});

const updateToolsOrder = async () => {
    try {
        const category_ids = resourcesRef.value.map(({ id }) => id);

        const data = {
            attributes: {
                'category-ids': category_ids
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
            `forum-categories/sort`,
            { data: { data } }
        );
    } catch (error) {
        STUDIP.Report.error(error);
    }
}
</script>


<template>
    <div class="lti">
        <header class="header">
            <div class="header__content header__content--with-actions">
                <h2>
                    {{ $gettext('LTI-Ressourcen') }}
                </h2>

                <div class="actions">
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

        <draggable
            v-model="resourcesRef"
            item-key="id"
            :animation="200"
            @end="alert('Dragged!')"
            :disabled="false"
            class="tools-card-container"
            handle=".drag-handle"
            tag="ul">
            <template #item="{element}">
                <li>
                    <ToolCard :tool="element" />
                </li>
            </template>
            <template #footer>
                <li key="footer">
                    <div class="studip-card studip-card--create-tool">
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
                </li>
            </template>
        </draggable>

        <form id="lti-resource-delete-form" method="post">
            <input type="hidden" :name="CSRF.name" :value="CSRF.value" />
        </form>
    </div>
</template>
