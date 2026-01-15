import { defineStore } from 'pinia';
import { ref } from 'vue';
import { COMMUNITY_EXPANDED_VIEWS as expandedViewsRegistry } from '@/vue/components/community/expanded-views/expandedViewRegistery.js';

export const useCommunityOverviewStore = defineStore('communityOverview', () => {
    const isDrawerOpen = ref(false);
    const drawerComponent = ref(null);
    const drawerProps = ref({});
    const drawerAttachTarget = ref(null);

    function setDrawerAttachTarget() {
        const targetElement = document.querySelector('#content-wrapper');
        if (targetElement) {
            drawerAttachTarget.value = targetElement;
        } else {
            console.warn("Das Drawer-Ziel #content-wrapper wurde im DOM nicht gefunden!");
        }
    }

    function openDrawer(component, props = {}) {
        console.log('props: ', props);
        if (!component) {
            console.error('openDrawer wurde ohne Komponente aufgerufen.');
            return;
        }
        drawerComponent.value = component;
        drawerProps.value = props;
        isDrawerOpen.value = true;
    }

    function closeDrawer() {
        isDrawerOpen.value = false;
    }

    function openChatInDrawer(threadId) {
        const ChatComponent = expandedViewsRegistry['chat'];
        console.log('theradId: ', threadId);
        if (!ChatComponent) {
            console.error('ChatExpandedView in Registry nicht gefunden!');
            return;
        }

        openDrawer(ChatComponent, {
            threadId: threadId,
        });
    }

    function openContactInDrawer(contactId) {
        const ContactComponent = expandedViewsRegistry['contact'];
        if (!ContactComponent) {
            console.error('ContactExpandedView in Registry nicht gefunden!');
            return;
        }

        openDrawer(ContactComponent, {
            contactId: contactId,
        });
    }

    return {
        isDrawerOpen,
        drawerComponent,
        drawerProps,
        drawerAttachTarget,
        setDrawerAttachTarget,
        openDrawer,
        closeDrawer,

        openChatInDrawer,
        openContactInDrawer,
    };
});
