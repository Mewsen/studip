<script setup>
import {computed, ref} from "vue";
import {$gettext} from "../../../../assets/javascripts/lib/gettext";
import StudipActionMenu from "../../StudipActionMenu.vue";
import {deleteResourceURL, editResourceConsentURL, editResourceURL, launchResourceURL} from "../helpers/urls";
import ResourceDetail from "./ResourceDetail.vue";
import StudipIcon from "../../StudipIcon.vue";
import {useLtiConfig} from "../../../store/pinia/lti/Config";

const ltiConfig = useLtiConfig();
const props= defineProps({
    tool: {
        type: Object,
        required: true
    }
});
const emit = defineEmits(['swap']);

const isResourceDetailDialogOpen = ref(false);

const title = computed(() => props.tool.title || props.tool.registration.name);
const description = computed(() => props.tool.description || props.tool.registration.description);


const actionMenus = computed(() => {
    const base = [
        { label: $gettext('Konfiguration anzeigen'),  icon: 'info', emit: 'show'},
        { label: $gettext('Datenschutzeinstellungen'),  icon: 'privacy', emit: 'editConsent'},
    ];

    if (ltiConfig.isModerator) {
        return [
            ...base,
            { label: $gettext('Bearbeiten'),  icon: 'edit', emit: 'edit'},
            { label: $gettext('Löschen'),  icon: 'trash', emit: 'delete'}
        ]
    }

    return base;
});

const resourceURL = computed(() => launchResourceURL(props.tool.id));
const launchContainer = computed(() => props.tool.container.value || props.tool.registration.container.value);
const isIframe = computed(() => launchContainer.value === 2);

const containerAttributes = computed(() => {
    if(isIframe.value) {
        return {}
    }

    return {
        title: $gettext('Anwendung starten'),
        href: resourceURL.value,
        target: '_blank'
    }
});
const showTool = () => isResourceDetailDialogOpen.value = true;

const editTool = () => STUDIP.Dialog.fromURL(editResourceURL(props.tool.id), {width: '700', height: '700'});
const editConsent = () => STUDIP.Dialog.fromURL(editResourceConsentURL(props.tool.id), {width: '700', height: '700'});

const showConfirmDelete = () => STUDIP.Dialog.confirm(
    $gettext('Wollen Sie diesen LTI-Ressource "%{name}" wirklich entfernen?', {name: title.value}),
    () => deleteTool(props.tool.id),
    STUDIP.Dialog.close()
);

const deleteTool = (id) => {
    const deleteForm = document.getElementById('lti-resource-delete-form');
    deleteForm.action = deleteResourceURL(id);
    deleteForm.submit();
}

const swap = event => {
    const keyCodes = ['ArrowLeft', 'ArrowUp', 'ArrowRight', 'ArrowDown'];

    if (keyCodes.includes(event.key)) {
        event.preventDefault();
        const step = (event.key === 'ArrowLeft' || event.key === 'ArrowUp') ? -1 : 1;
        emit('swap', props.tool.id, step);
    }
}
</script>

<template>
    <component
        :is="isIframe ? 'div' : 'a'"
        class="tool-card"
        :class="{ 'tool-card--iframe': isIframe }"
        v-bind="containerAttributes"
    >
        <div  class="tool-card__flag" v-if="tool.color" :style="{ backgroundColor: tool.color}">
        </div>
        <div class="studip-card">
            <header class="studip-card__header">
                <p class="studip-card__title">
                    <StudipIcon v-if="tool.icon" :shape="tool.icon" :size="60" />
                    {{ title }}
                </p>

                <div class="studip-card__actions">
                    <StudipActionMenu
                        :context="title"
                        :items="actionMenus"
                        @show="showTool"
                        @edit="editTool"
                        @editConsent="editConsent"
                        @delete="showConfirmDelete"
                    />
                </div>
            </header>

            <div class="studip-card__body">
                <p v-if="!isIframe" class="studip-card__description">
                    {{ description }}
                </p>

                <iframe
                    v-if="isIframe"
                    :src="resourceURL"
                ></iframe>
            </div>

            <footer v-if="ltiConfig.isModerator" class="studip-card__footer">
                <div class="drag-area">
                    <a class="drag-link"
                       tabindex="0"
                       role="option"
                       :title="$gettext('Sortierelement für Element %{name}. Drücken Sie die Tasten Pfeil-nach-oben oder Pfeil-nach-unten, um dieses Element in der Liste zu verschieben.', {name: title})"
                       :id="`sort-handle-${tool.id}`"
                       @keydown="swap">
                        <span class="drag-handle"></span>
                    </a>
                </div>
            </footer>
        </div>
    </component>
    <ResourceDetail :resource="tool" v-model:isOpen="isResourceDetailDialogOpen" />
</template>
