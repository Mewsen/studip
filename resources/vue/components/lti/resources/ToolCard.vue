<script setup>
import {computed} from "vue";
import {$gettext} from "../../../../assets/javascripts/lib/gettext";
import StudipActionMenu from "../../StudipActionMenu.vue";
import {
    deleteResourceURL,
    editResourceConsentURL,
    editResourceURL,
    launchResourceURL
} from "../helpers/urls";
import StudipIcon from "../../StudipIcon.vue";
import {useLtiConfig} from "../../../store/pinia/lti/Config";

const ltiConfig = useLtiConfig();
const props= defineProps({
    resource: {
        type: Object,
        required: true
    }
});
const emit = defineEmits(['swap', 'showResource']);

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

const resourceURL = computed(() => launchResourceURL(props.resource.id, props.resource.registration.version));

const launchContainer = computed(() => props.resource.launch_container || props.resource.registration.meta.configs.launch_container);
const isIframe = computed(() => launchContainer.value === 'iframe' && props.resource.registration.status !== 'inactive');

const containerAttributes = computed(() => {
    if(isIframe.value) {
        return {}
    }

    if (props.resource.registration.status === 'inactive') {
        const title =  $gettext('Die LTI-Registrierung „%{name}“ ist deaktiviert.', { name: props.resource.registration.name });
        return {
            title,
            ariaLabel: title,
            href: '#'
        }
    }

    const title = $gettext('Anwendung starten');
    return {
        title,
        ariaLabel: title,
        href: resourceURL.value,
        target: '_blank'
    }
});
const editTool = () => STUDIP.Dialog.fromURL(editResourceURL(props.resource.id), {width: '700', height: '750'});
const editConsent = () => STUDIP.Dialog.fromURL(editResourceConsentURL(props.resource.id), {width: '700', height: '750'});

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
        <div class="tool-card__flag" v-if="resource.color" :style="{ backgroundColor: resource.color}">
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
                        @show="emit('showResource', resource.id)"
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
                    loading="lazy"
                    class="lti-content"
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
                <div v-if="resource.registration.status === 'inactive'" class="flex items-center gap-5" style="color: var(--color--warning)">
                    <StudipIcon shape="exclaim-circle" :size="20" aria-hidden="true" />
                    {{ $gettext('Deaktiviert') }}
                </div>
            </footer>
        </div>
    </component>
</template>
