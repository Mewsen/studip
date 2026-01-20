import { computed, ref } from 'vue';
import { useContactStore } from '@/vue/store/pinia/contact/contacts';
import { useContactGroupStore } from '@/vue/store/pinia/contact/contact-groups';

export function useContactActions(gettext) {
    const contactStore = useContactStore();
    const contactGroupStore = useContactGroupStore();

    const isConfirmDialogOpen = ref(false);
    const isProcessing = ref(false);
    const confirmConfig = ref({
        title: '',
        question: '',
        action: null,
        width: '420',
        height: '200',
    });

    const canCall = computed(() => {
        return window.matchMedia('(pointer: coarse)').matches || /Android|iPhone|iPad|iPod/i.test(navigator.userAgent);
    });

    const openDeleteDialog = (contact) => {
        confirmConfig.value = {
            title: gettext('Kontakt löschen'),
            question: gettext('Möchten Sie den Kontakt wirklich löschen?'),
            action: () => contactStore.removeContact(STUDIP.USER_ID, contact.id),
            width: '420',
            height: '200',
        };
        isConfirmDialogOpen.value = true;
    };

    const openRemoveFromGroupDialog = (contact) => {
        const groupName = contactGroupStore.selectGroup.name;
        confirmConfig.value = {
            title: gettext('Kontakt aus Gruppe löschen'),
            question: gettext('Möchten Sie den Kontakt aus der Gruppe %{groupName} unwiderruflich löschen?', {
                groupName,
            }),
            action: () => contactGroupStore.removeUserFromGroup(contactGroupStore.selectedGroupId, contact.id),
            width: '420',
            height: '200',
        };
        isConfirmDialogOpen.value = true;
    };

    const handleConfirmAction = async () => {
        if (!confirmConfig.value.action) return;

        isProcessing.value = true;
        try {
            await confirmConfig.value.action();
            isConfirmDialogOpen.value = false;
        } catch (error) {
            console.error('Fehler bei Dialog-Aktion:', error);
        } finally {
            isProcessing.value = false;
        }
    };

    const getProfileUrl = (contact) => `${STUDIP.URLHelper.base_url}dispatch.php/profile?username=${contact.username}`;

    const getMessageUrl = (contact) =>
        `${STUDIP.URLHelper.base_url}dispatch.php/messages/write?rec_uname=${contact.username}`;

    const getChatUrl = (contact) => `${STUDIP.URLHelper.base_url}dispatch.php/blubber/write_to/${contact.id}`;

    const getMenuItems = (contact) => {
        const menuItems = [
            {
                label: gettext('vCard herunterladen'),
                icon: 'vcard',
                type: 'link',
                url: contact.meta['vcard-download-link'],
            },
        ];

        if (contactGroupStore.selectedGroupId !== 'all') {
            menuItems.push({
                label: gettext('Aus Gruppe entfernen'),
                icon: 'remove-circle',
                emit: 'delete-from-group',
            });
        }

        menuItems.push({
            label: gettext('Kontakt löschen'),
            icon: 'trash',
            emit: 'delete',
        });

        return menuItems;
    };

    return {
        isConfirmDialogOpen,
        isProcessing,
        confirmConfig,
        openDeleteDialog,
        openRemoveFromGroupDialog,
        handleConfirmAction,
        canCall,
        getProfileUrl,
        getMessageUrl,
        getChatUrl,
        getMenuItems,
    };
}
