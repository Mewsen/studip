<template>
    <GridLayout
        v-if="isMiscLoaded"
        v-model:layout="layout"
        :breakpoints="miscStore.breakpointsWidth"
        :cols="miscStore.breakpointsCols"
        :responsive-layouts="presetLayouts"
        :row-height="rowHeight"
        :is-draggable="draggable"
        :is-resizable="resizable"
        :responsive="true"
        use-css-transforms
        @breakpoint-changed="onBreakpointChanged"
    >
        <GridItem
            v-for="item in layout"
            :key="item.i"
            :x="item.x"
            :y="item.y"
            :w="item.w"
            :h="item.h"
            :i="item.i"
            :tabindex="draggable ? 0 : -1"
            @keydown="handleKeyboardLayoutChange($event, item.i)"
            :role="draggable ? 'application' : 'region'"
            :aria-label="
                draggable
                    ? $gettext(
                          'Widget ist verschiebbar. Benutze Pfeiltasten zum Verschieben und Shift + Pfeiltasten zum Anpassen der Größe.'
                      )
                    : null
            "
        >
            <widget-renderer
                :widget-id="item.i"
                :widget-components="props.widgetComponents"
                :is-editing="draggable"
                @delete-widget="onDeleteWidget"
                @update-config="onUpdateWidget"
            />
        </GridItem>
    </GridLayout>
    <div v-if="showLoadingError && !isMiscLoaded">
        <p>{{ $gettext('Layout-Einstellungen konnten nicht geladen werden.') }}</p>
    </div>
</template>

<script setup>
import { computed, onBeforeMount, ref, watch } from 'vue';
import { GridLayout, GridItem } from 'grid-layout-plus';

import WidgetRenderer from '@/vue/components/widget/WidgetRenderer.vue';

import { useContainerStore } from '@/vue/store/pinia/widget/dashboard-widget-containers.js';
import { useWidgetMiscStore } from '@/vue/store/pinia/widget/dashboard-widget-misc.js';

const miscStore = useWidgetMiscStore();
const containerStore = useContainerStore();

const props = defineProps({
    widgetComponents: {
        type: Object,
        required: true,
    },
    rowHeight: {
        type: Number,
        default: 60
    }
});

const isMiscLoaded = ref(false);
const showLoadingError = ref(false);
const currentBreakpoint = ref(null);
const draggable = ref(true);
const resizable = ref(true);
let loadingTimer = null;

onBeforeMount(async () => {
    loadingTimer = setTimeout(() => {
        showLoadingError.value = true;
    }, 800);
    await miscStore.fetchMisc();
    isMiscLoaded.value = true;
    clearTimeout(loadingTimer);
    showLoadingError.value = false;
});

watch(
    () => miscStore.breakpoints,
    (bps) => {
        if (isMiscLoaded.value && bps && !currentBreakpoint.value) {
            currentBreakpoint.value = bps[bps.length - 1];
        }
    },
    { immediate: true }
);

const layout = computed({
    get() {
        if (!currentBreakpoint.value) return [];
        return containerStore.layoutForBreakpoint(currentBreakpoint.value);
    },
    set(newLayout) {
        containerStore.updateLayout(currentBreakpoint.value, newLayout);
    },
});

function onBreakpointChanged(bp) {
    currentBreakpoint.value = bp;
}

function onDeleteWidget() {
    //todo
}

function onUpdateWidget() {
    //todo
}

function handleKeyboardLayoutChange(event, itemId) {
    if (!draggable.value) return;

    const itemIndex = layout.value.findIndex((item) => item.i === itemId);
    if (itemIndex === -1) return;

    const item = layout.value[itemIndex];
    let changed = false;

    const isArrowKey = ['ArrowUp', 'ArrowDown', 'ArrowLeft', 'ArrowRight'].includes(event.key);
    if (isArrowKey) {
        event.preventDefault();
    }

    if (!event.shiftKey) {
        switch (event.key) {
            case 'ArrowUp':
                item.y = Math.max(0, item.y - 1);
                changed = true;
                break;
            case 'ArrowDown':
                item.y += 1;
                changed = true;
                break;
            case 'ArrowLeft':
                item.x = Math.max(0, item.x - 1);
                changed = true;
                break;
            case 'ArrowRight':
                item.x += 1;
                changed = true;
                break;
        }
    } else if (resizable.value) {
        switch (event.key) {
            case 'ArrowUp':
                item.h = Math.max(1, item.h - 1);
                changed = true;
                break;
            case 'ArrowDown':
                item.h += 1;
                changed = true;
                break;
            case 'ArrowLeft':
                item.w = Math.max(1, item.w - 1);
                changed = true;
                break;
            case 'ArrowRight':
                item.w += 1;
                changed = true;
                break;
        }
    }

    if (changed) {
        layout.value = [...layout.value];
        // todo: store it in db!
    }
}
</script>