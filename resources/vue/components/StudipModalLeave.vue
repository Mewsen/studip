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
    window.removeEventListener('beforeunload', onBeforeUnload);
    showModal.value = false;
    window.location.href = pendingHref.value;
}

function cancelLeave() {
    showModal.value = false;
    pendingHref.value = null;
}

function onBeforeUnload(e) {
    if (props.hasUnsavedChanges) {
        e.preventDefault();
        e.returnValue = '';
        return '';
    }
}
async function saveAndLeave() {
    if (typeof props.onSave === 'function') {
        try {
            await props.onSave();
            window.removeEventListener('beforeunload', onBeforeUnload);
            window.location.href = pendingHref.value;
        } catch (e) {
            console.error('Speichern fehlgeschlagen:', e);
        }
    }
}

onMounted(() => {
    document.addEventListener('click', onClick);
    window.addEventListener('beforeunload', onBeforeUnload);
});

onUnmounted(() => {
    document.removeEventListener('click', onClick);
    window.removeEventListener('beforeunload', onBeforeUnload);
});
</script>
