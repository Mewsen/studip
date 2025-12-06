import ChatSingleWidget from '@/vue/components/community/widgets/ChatSingleWidget.vue';
import ChatRecentWidget from '@/vue/components/community/widgets/ChatRecentWidget.vue';
import ChatConversationWidget from '@/vue/components/community/widgets/ChatConversationWidget.vue';
import ChatSelectionWidget from '@/vue/components/community/widgets/ChatSelectionWidget.vue';

import ContactSingleWidget from '@/vue/components/community/widgets/ContactSingleWidget.vue';
import ContactGroupWidget from '@/vue/components/community/widgets/ContactGroupWidget.vue';
import ContactSelectionWidget from '@/vue/components/community/widgets/ContactSelectionWidget.vue';

import GroupSingleWidget from '@/vue/components/community/widgets/GroupSingleWidget.vue';
import GroupRecentWidget from '@/vue/components/community/widgets/GroupRecentWidget.vue';
import GroupPinboardWidget from '@/vue/components/community/widgets/GroupPinboardWidget.vue';

export const COMMUNITY_WIDGETS = {
    'chat.single': ChatSingleWidget,
    'chat.recent': ChatRecentWidget,
    'chat.conversation': ChatConversationWidget,
    'chat.selection' : ChatSelectionWidget,

    'contact.single': ContactSingleWidget,
    'contact.group': ContactGroupWidget,
    'contact.selection': ContactSelectionWidget,

    'group.single': GroupSingleWidget,
    'group.recent': GroupRecentWidget,
    'group.pinboard': GroupPinboardWidget,
};
