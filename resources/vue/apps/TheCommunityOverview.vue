<template>
    <content-bar :title="$gettext('Community')" :is-content-bar="true" icon="community">
        <template #buttons-right>
            <studip-context-menu :title="$gettext('Hinzufügen')" button-shape="add">
                <template #content> testing... </template>
            </studip-context-menu>
        </template>
    </content-bar>
    <template v-if="isDashboardReady && hasLayout">
        <studip-widget-dashboard
            :widget-components="communityWidgetRegistry"
            :initial-layout-data="containerStore.getLayout"
            v-bind="dashboardMiscProps"
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
            <component :is="overviewStore.drawerComponent" v-bind="overviewStore.drawerProps" />
        </studip-drawer>
    </template>
    <template v-if="!isDashboardReady">
        <studip-progress-indicator v-show="showLoading" :description="$gettext('Widgets werden geladen…')" />
    </template>
    <div v-if="isDashboardReady && !hasLayout">
        {{ $gettext('keine Widgets gefunden!!!') }}
    </div>
</template>
<script setup>
import { computed, onMounted, ref } from 'vue';
import StudipWidgetDashboard from '@/vue/components/StudipWidgetDashboard.vue';
import StudipDrawer from '@/vue/components/StudipDrawer.vue';
import ContentBar from '@/vue/components/ContentBar.vue';
import StudipContextMenu from '@/vue/components/StudipContextMenu.vue';
import StudipProgressIndicator from '@/vue/components/StudipProgressIndicator.vue';

import { useCommunityOverviewStore } from '@/vue/store/pinia/community/community-overview.js';
import { useContainerStore } from '@/vue/store/pinia/widget/dashboard-widget-containers.js';
import { useWidgetStore } from '@/vue/store/pinia/widget/dashboard-widgets.js';
import { useWidgetMiscStore } from '@/vue/store/pinia/widget/dashboard-widget-misc.js';

import { COMMUNITY_WIDGETS as communityWidgetRegistry } from '@/vue/components/community/widgets/widgetRegistry.js';

const overviewStore = useCommunityOverviewStore();
const containerStore = useContainerStore();
const widgetStore = useWidgetStore();
const widgetMiscStore = useWidgetMiscStore();

const dashboardMiscProps = computed(() => {
    if (widgetMiscStore.breakpointsWidth && widgetMiscStore.breakpointsCols && widgetMiscStore.breakpoints) {
        return {
            breakpoints: widgetMiscStore.breakpointsWidth,
            cols: widgetMiscStore.breakpointsCols,
            breakpointOrder: widgetMiscStore.breakpoints,
        };
    }
    return {};
});

const isDashboardReady = ref(false);
const showLoading = ref(false);
let loadingTimer = null;
const hasLayout = computed(() => containerStore.hasLayout);

onMounted(async () => {
    overviewStore.setDrawerAttachTarget();
    loadingTimer = setTimeout(() => {
        showLoading.value = true;
    }, 800);
    
    await widgetMiscStore.fetchMisc();
    await containerStore.fetchOrCreateContainer('community', '');

    if (containerStore.container && containerStore.container.payload) {
        isDashboardReady.value = true;
    }
    clearTimeout(loadingTimer);
    showLoading.value = false;
});
</script>
