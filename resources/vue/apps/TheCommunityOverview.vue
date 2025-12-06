<template>
    <studip-widget-dashboard
        :title="$gettext('Community')"
        :widget-components="communityWidgetRegistry"
        :initial-layout-data="communityLayoutData"
    >
    </studip-widget-dashboard>
    <studip-drawer
        v-if="overviewStore.drawerAttachTarget"
        side="right"
        width="570px"
        :displayOverlay="true"
        :attachTo="overviewStore.drawerAttachTarget"
        :visible="overviewStore.isDrawerOpen"
        @close="overviewStore.closeDrawer"
    >
        <component
            :is="overviewStore.drawerComponent"
            v-bind="overviewStore.drawerProps" 
        />
    </studip-drawer>
</template>
<script setup>
import { onMounted } from 'vue';
import StudipWidgetDashboard from '@/vue/components/StudipWidgetDashboard.vue';
import StudipDrawer from '@/vue/components/StudipDrawer.vue';

import { useCommunityOverviewStore } from '@/vue/store/pinia/community/communityOverview.js';

import { COMMUNITY_WIDGETS as communityWidgetRegistry } from '@/vue/components/community/widgets/widgetRegistry.js';

const overviewStore = useCommunityOverviewStore();

const communityLayoutData = {
    xxs: [],
    xs: [],
    sm: [],
    md: [],
    lg: [
        {
            x: 0,
            y: 0,
            w: 4,
            h: 5,
            i: '42',
            data: {
                type: 'contact.single',
                config: { contactId: 'uuid-max-mustermann', showRole: true },
            },
        },
        {
            x: 4,
            y: 0,
            w: 4,
            h: 3,
            i: '43',
            data: {
                type: 'group.single',
                config: { groupId: '99' },
            },
        },
        {
            x: 8,
            y: 0,
            w: 4,
            h: 3,
            i: '44',
            data: {
                type: 'chat.single',
                config: { threadId: '42' },
            },
        }
    ],
    xl: [],
    xxl: [],
};

onMounted(() => {
    overviewStore.setDrawerAttachTarget();
});
</script>
