<script setup>
import {computed} from "vue";
import {$gettext} from "../../../../assets/javascripts/lib/gettext";
import StudipActionMenu from "../../StudipActionMenu.vue";
import {getCategoryDeleteURL, getCategoryEditURL} from "../../forum/helpers/urls";
import {deleteDeploymentURL, deleteResourceURL, editResourceURL} from "../helpers/urls";

const props= defineProps({
    tool: {
        type: Object,
        required: true
    }
});
const emit = defineEmits(['swap']);

const title = computed(() => props.tool.title || props.tool.registration.name);
const description = computed(() => props.tool.description || props.tool.registration.description);


const isModerator = true;


const actionMenus = computed(() => {
    const base = [
        { label: $gettext('Konfiguration anzeigen'),  icon: 'info', emit: 'showConfig'},
    ];

    if (isModerator) {
        return [
            ...base,
            { label: $gettext('Bearbeiten'),  icon: 'edit', emit: 'edit'},
            { label: $gettext('Löschen'),  icon: 'trash', emit: 'delete'}
        ]
    }

    return base;
});

const showConfig = () => {}

const editTool = () => STUDIP.Dialog.fromURL(editResourceURL(props.tool.id), {width: '700', height: '700'});

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
    <a
        href="#"
        :title="$gettext('Anwendung starten')"
        class="studip-card">
        <header class="studip-card__header">
            <h3 class="studip-card__title">{{ title }}</h3>

            <div class="studip-card__actions">
                <StudipActionMenu
                    :context="title"
                    :items="actionMenus"
                    @showConfig="showConfig"
                    @edit="editTool"
                    @delete="showConfirmDelete"
                />
            </div>
        </header>

        <div class="studip-card__body">
            <p class="studip-card__description">
                {{ description }}
            </p>
        </div>

        <footer class="studip-card__footer">
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
    </a>
</template>
