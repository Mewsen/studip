<script setup>
import {computed, ref} from "vue";
import {$gettext} from "../../../../assets/javascripts/lib/gettext";
import StudipActionMenu from "../../StudipActionMenu.vue";
import {
    deleteResourceURL,
    editResourceConsentURL,
    editResourceURL,
    launchResourceURL,
    selectContentURL
} from "../helpers/urls";
import ResourceDetail from "./ResourceDetail.vue";
import StudipIcon from "../../StudipIcon.vue";
import {useLtiConfig} from "../../../store/pinia/lti/Config";
import StudipTooltipIcon from "../../StudipTooltipIcon.vue";

const ltiConfig = useLtiConfig();
const props= defineProps({
    resource: {
        type: Object,
        required: true
    }
});
const emit = defineEmits(['swap']);

const isResourceDetailDialogOpen = ref(false);

const title = computed(() => props.resource.title || props.resource.registration.name);

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

const resourceURL = computed(() => {
    if (props.resource.launch_type === 'deep_linking') {
        return selectContentURL(props.resource.id);
    }

    return launchResourceURL(props.resource.id);
});
const launchContainer = computed(() => props.resource.container.value || props.resource.registration.container.value);
const isIframe = computed(() => launchContainer.value === 'iframe');

const containerAttributes = computed(() => {
    if(isIframe.value) {
        return {}
    }

    return {
        title: props.resource.launch_type === 'deep_linking' ? $gettext('Inhalts auswählen') : $gettext('Anwendung starten'),
        href: resourceURL.value,
        target: '_blank'
    }
});
const showTool = () => isResourceDetailDialogOpen.value = true;

const editTool = () => STUDIP.Dialog.fromURL(editResourceURL(props.resource.id), {width: '700', height: '700'});
const editConsent = () => STUDIP.Dialog.fromURL(editResourceConsentURL(props.resource.id), {width: '700', height: '700'});

const showConfirmDelete = () => STUDIP.Dialog.confirm(
    $gettext('Wollen Sie diesen LTI-Ressource "%{name}" wirklich entfernen?', {name: title.value}),
    () => deleteTool(props.resource.id),
    STUDIP.Dialog.close()
);

const deleteTool = id => {
    const deleteForm = document.getElementById('lti-resource-delete-form');
    deleteForm.action = deleteResourceURL(id);
    deleteForm.submit();
}

const swap = event => {
    const keyCodes = ['ArrowLeft', 'ArrowUp', 'ArrowRight', 'ArrowDown'];

    if (keyCodes.includes(event.key)) {
        event.preventDefault();
        const step = (event.key === 'ArrowLeft' || event.key === 'ArrowUp') ? -1 : 1;
        emit('swap', props.resource.id, step);
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
        <div  class="tool-card__flag" v-if="resource.color" :style="{ backgroundColor: resource.color}">
        </div>
        <div class="studip-card">
            <header class="studip-card__header">
                <p class="studip-card__title">
                    <StudipIcon v-if="resource.icon" :shape="resource.icon" :size="60" />
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
                <p v-if="!isIframe" class="studip-card__description" v-html="resource.description"></p>

                <iframe
                    v-if="isIframe"
                    :src="resourceURL"
                ></iframe>
            </div>

            <footer v-if="ltiConfig.isModerator" class="studip-card__footer">
                <div class="drag-area">
                    <a
                       :id="`sort-handle-${resource.id}`"
                       class="drag-link"
                       tabindex="0"
                       role="option"
                       :title="$gettext('Sortierelement für Element %{name}. Drücken Sie die Tasten Pfeil-nach-oben oder Pfeil-nach-unten, um dieses Element in der Liste zu verschieben.', {name: title})"
                       @keydown="swap">
                        <span class="drag-handle"></span>
                    </a>
                </div>
                <div v-if="resource.launch_type === 'deep_linking'" class="flex items-center gap-5">
                    {{ $gettext('LTI Deep Linking noch nicht fertig eingerichtet') }}
                    <StudipTooltipIcon
                        :text="$gettext('Deployment-ID: %{id}', {id: resource.deployment.deployment_key})"
                    />
                </div>
            </footer>
        </div>
    </component>
    <ResourceDetail :resource="resource" v-model:isOpen="isResourceDetailDialogOpen" />
</template>
