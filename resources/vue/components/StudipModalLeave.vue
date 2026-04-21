<template>
    <StudipDialog
        v-if="showModal"
        width="600"
        height="200"
        @confirm="confirmLeave"
        @close="cancelLeave"
        :closeClass="false"
        :closeText="$gettext('Auf Seite bleiben')"
        :confirmText="$gettext('Seite verlassen')"
        :title="$gettext('Ungespeicherte Änderungen')"
        :question="$gettext('Es gibt ungespeicherte Änderungen. Möchten Sie die Seite wirklich verlassen?')"
    >
        <template #dialogButtons>
            <button v-if="props.onSave" class="button" @click="saveAndLeave">
                {{ $gettext('Speichern & Verlassen') }}
            </button>
        </template>
    </StudipDialog>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import StudipDialog from './StudipDialog.vue';
import {useSecurityHandler} from "../composables/useSecurityHandler";

const props = defineProps({
    hasUnsavedChanges: {
        type: Boolean,
        required: true,
    },
    onSave: {
        type: Function,
        required: false,
    },
});

const showModal = ref(false);
const pendingHref = ref(null);
const securityHandler = useSecurityHandler(() => props.hasUnsavedChanges);

function onClick(e) {
    const link = e.target.closest('a[href]');
    if (!link || link.target === '_blank') {
        return;
    }

    const href = link.href;

    if (props.hasUnsavedChanges) {
        e.preventDefault();
        pendingHref.value = href;
        showModal.value = true;
    }
}

function confirmLeave() {
    securityHandler.deactivate();
    showModal.value = false;
    window.location.href = pendingHref.value;
}

function cancelLeave() {
    showModal.value = false;
    pendingHref.value = null;
}

async function saveAndLeave() {
    if (typeof props.onSave === 'function') {
        try {
            await props.onSave();
            securityHandler.deactivate();
            window.location.href = pendingHref.value;
        } catch (e) {
            console.error('Speichern fehlgeschlagen:', e);
        }
    }
}

onMounted(() => {
    document.addEventListener('click', onClick);
});

onUnmounted(() => {
    document.removeEventListener('click', onClick);
});
</script>
