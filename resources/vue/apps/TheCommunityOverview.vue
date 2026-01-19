<template>
    <content-bar :title="$gettext('Community')" :is-content-bar="true" icon="community">
        <template #buttons-right>
            <studip-context-menu :title="$gettext('Hinzufügen')" button-shape="add">
                <template #content>
                    <button class="button" @click="addWidget">ADD ITEM</button>
                </template>
            </studip-context-menu>
        </template>
    </content-bar>
    <template v-if="isWidgetsLoaded && hasLayout">
        <studip-widget-dashboard :widget-components="communityWidgetRegistry"> </studip-widget-dashboard>
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
    <template v-if="!isWidgetsLoaded">
        <studip-progress-indicator v-show="showLoading" :description="$gettext('Widgets werden geladen…')" />
    </template>
    <div v-if="isWidgetsLoaded && !hasLayout">
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

import { useLoadingBuffer } from '@/vue/composables/useLoadingBuffer.js';

import { useCommunityOverviewStore } from '@/vue/store/pinia/community/community-overview.js';
import { useContainerStore } from '@/vue/store/pinia/widget/dashboard-widget-containers.js';
import { useWidgetMiscStore } from '@/vue/store/pinia/widget/dashboard-widget-misc.js';

import { COMMUNITY_WIDGETS as communityWidgetRegistry } from '@/vue/components/community/widgets/widgetRegistry.js';

const overviewStore = useCommunityOverviewStore();
const containerStore = useContainerStore();
const miscStore = useWidgetMiscStore();

const isWidgetsLoaded = ref(false);
const hasLayout = computed(() => containerStore.hasLayout);

const { showLoading, runWithLoading } = useLoadingBuffer(800);

onMounted(async () => {
    overviewStore.setDrawerAttachTarget();
    runWithLoading(async () => {
        await containerStore.fetchOrCreateContainer('community', '');
        await containerStore.fetchContainerWidgets(containerStore.container.id);

        if (containerStore.container && containerStore.container.payload) {
            isWidgetsLoaded.value = true;
        }
    });
});

async function addWidget() {
    const widgetType = 'contact';
    const widgetScope = 'single';
    const payload = { 'contact-id': '666' };
    const position = { x: 0, y: 0, w: 2, h: 2 };
    const breakpoint = 'lg';
    await containerStore.addWidget(widgetType, widgetScope, payload, position, breakpoint);
}
</script>
