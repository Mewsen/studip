import { ref, computed } from 'vue';
import { useContactStore } from '@/vue/store/pinia/contact/contacts';
import { useContactGroupStore } from '@/vue/store/pinia/contact/contact-groups';

export function useContactDialogActions(gettext) {
    const contactStore = useContactStore();
    const contactGroupStore = useContactGroupStore();

    // --- DIALOG STATE ---
    const activeDialog = ref(null);
    const isProcessing = ref(false);

    // --- FORM STATES ---
    const groupNameModel = ref(''); // Teilt sich Add & Edit
    const selectedUsers = ref([]);  // MultiPersonSearch
    const addToGroupIds = ref([]);  // DualListBox

    const openDialog = (type, initialData = '') => {
        activeDialog.value = type;
        groupNameModel.value = initialData;
        selectedUsers.value = [];
        addToGroupIds.value = [];
    };

    const closeDialog = () => {
        activeDialog.value = null;
        isProcessing.value = false;
    };

    // --- ACTIONS ---
    const actions = {
        addContact: async () => {
            await contactStore.addContacts(STUDIP.USER_ID, selectedUsers.value);
        },
        addGroup: async () => {
            if (!groupNameModel.value) return;
            const added = await contactGroupStore.addContactGroup(groupNameModel.value);
            notify(added, gettext('Gruppe wurde erfolgreich hinzugefügt.'), gettext('Fehler beim Erstellen.'));
        },
        editGroup: async () => {
            const updated = await contactGroupStore.updateContactGroup(
                contactGroupStore.selectedGroupId, 
                groupNameModel.value
            );
            notify(updated, gettext('Gruppe wurde erfolgreich aktualisiert.'), gettext('Fehler beim Update.'));
        },
        addToGroup: async () => {
            await contactGroupStore.addMultipleUsersToGroup(
                contactGroupStore.selectedGroupId, 
                addToGroupIds.value
            );
        }
    };

    const handleConfirm = async () => {
        if (!actions[activeDialog.value]) return;
        
        isProcessing.value = true;
        try {
            await actions[activeDialog.value]();
            closeDialog();
        } finally {
            isProcessing.value = false;
        }
    };

    // Helfer für Benachrichtigungen
    const notify = (success, successMsg, errorMsg) => {
        STUDIP.eventBus.emit('push-system-notification', {
            type: success ? 'success' : 'error',
            message: success ? successMsg : errorMsg
        });
    };

    const dialogConfigs = {
    addContact: { title: gettext('Kontakt hinzufügen'), confirmText: gettext('Hinzufügen'), confirmClass: 'add', closeText: gettext('Abbrechen'), closeClass: 'cancel', height: 600, width: 750 },
    addGroup:    { title: gettext('Kontaktgruppe hinzufügen'),  confirmText: gettext('Hinzufügen'), confirmClass: 'add', closeText: gettext('Abbrechen'), closeClass: 'cancel',height: 240, width: 400 },
    editGroup:   { title: gettext('Kontaktgruppe bearbeiten'),  confirmText: gettext('Speichern'), confirmClass: 'accept', closeText: gettext('Abbrechen'), closeClass: 'cancel',height: 240, width: 400 },
    addToGroup:  { title: gettext('Kontakte hinzufügen'),  confirmText: gettext('Hinzufügen'), confirmClass: 'add', closeText: gettext('Abbrechen'), closeClass: 'cancel',height: 600, width: 750 }
};
const currentConfig = computed(() => dialogConfigs[activeDialog.value] || {});

    return {
        activeDialog, isProcessing, groupNameModel, selectedUsers, addToGroupIds, currentConfig,
        openDialog, closeDialog, handleConfirm
    };
}