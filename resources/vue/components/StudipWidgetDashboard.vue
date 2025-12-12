<template>
    <GridLayout
        v-model:layout="layout"
        :responsive-layouts="presetLayouts"
        :row-height="30"
        :is-draggable="draggable"
        :is-resizable="resizable"
        responsive
        :breakpoints="breakpoints"
        :cols="cols"
        use-css-transforms
        @breakpoint-changed="breakpointChangedEvent"
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
            <component
                :is="getWidgetComponent(item.data.type)"
                v-if="item.data && item.data.type && getWidgetComponent(item.data.type)"
                :widget-id="item.i"
                :widget-data="item.data"
                :initial-config="item.data.config"
                :is-editing="draggable"
                @update-config="handleWidgetUpdate"
                @delete-widget="handleWidgetDelete"
            />

            <div v-else class="widget-error-container">
                Widget "{{ item.data?.type || 'UNBEKANNT' }}" konnte nicht geladen werden.
            </div>
        </GridItem>
    </GridLayout>
</template>

<script setup lang="ts">
import { ref, reactive, type PropType } from 'vue';

import { GridLayout, GridItem } from 'grid-layout-plus';
import type { Breakpoint, LayoutItem as BaseLayoutItem } from 'grid-layout-plus';

type BreakpointKey = 'xxs' | 'xs' | 'sm' | 'md' | 'lg' | 'xl' | 'xxl';
type BreakpointMap = Record<BreakpointKey, number>;
interface WidgetData {
    type: string;
    config: Record<string, any>;
}
interface CustomLayoutItem extends BaseLayoutItem {
    i: string;
    data: WidgetData;
}
type Layout = CustomLayoutItem[];
type PresetLayoutMap = Record<string, Layout>;
type WidgetComponentMap = Record<string, any>;

const props = defineProps({
    widgetComponents: {
        type: Object as PropType<WidgetComponentMap>,
        required: true,
    },
    initialLayoutData: {
        type: Object as PropType<Record<string, Layout>>,
        default: () => ({}),
    },
    breakpointOrder: {
        type: Array as PropType<BreakpointKey[]>,
        default: () => ['xxl', 'xl', 'lg', 'md', 'sm', 'xs', 'xxs'],
    },
    breakpoints: {
        type: Object as PropType<BreakpointMap>,
        default: () => ({
            xxs: 0,
            xs: 400,
            sm: 768,
            md: 990,
            lg: 1410, // 1440px - 15px padding
            xl: 1890, // 1920px - 15px padding
            xxl: 2530, // 2560 - 15px padding
        }),
    },
    cols: {
        type: Object as PropType<BreakpointMap>,
        default: () => ({
            xxs: 2,
            xs: 4,
            sm: 8,
            md: 10,
            lg: 12,
            xl: 16,
            xxl: 20,
        }),
    },
});

const presetLayouts = reactive<PresetLayoutMap>(props.initialLayoutData);

function breakpointChangedEvent(newBreakpoint: Breakpoint, newLayout: Layout) {
    // console.info('BREAKPOINT CHANGED breakpoint=', newBreakpoint, ', layout: ', newLayout);
}

function getWidgetComponent(type: string): any {
    return props.widgetComponents[type];
}

const generateFlowLayout = (baseLayout: Layout, newCols: number): Layout => {
    let newLayout: Layout = [];
    let currentY = 0;
    let currentX = 0;
    let maxHInCurrentRow = 0;

    const sortedBaseLayout = [...baseLayout].sort((a, b) => {
        if (a.y !== b.y) {
            return a.y - b.y;
        }
        return a.x - b.x;
    });

    sortedBaseLayout.forEach((item) => {
        const newW = Math.min(item.w, newCols);

        if (currentX + newW > newCols) {
            currentY += maxHInCurrentRow;
            currentX = 0;
            maxHInCurrentRow = 0;
        }

        newLayout.push({
            ...item,
            x: currentX,
            y: currentY,
            w: newW,
        });

        currentX += newW;
        maxHInCurrentRow = Math.max(maxHInCurrentRow, item.h);
    });

    return newLayout;
};

function initializeResponsiveLayouts() {
    const breakpointOrder = props.breakpointOrder;
    const cols = props.cols;
    let baseLayout: Layout | null = null;
    let baseBreakpoint: BreakpointKey | null = null;

    type BreakpointKey = keyof typeof cols;

    for (let i = breakpointOrder.length - 1; i >= 0; i--) {
        const bpKeyString = breakpointOrder[i];

        const bpKey = bpKeyString as BreakpointKey;

        if (presetLayouts[bpKey] && presetLayouts[bpKey].length > 0) {
            baseLayout = presetLayouts[bpKey];
            baseBreakpoint = bpKeyString;
            break;
        }
    }

    if (!baseLayout || !baseBreakpoint) {
        console.error('FEHLER: Es konnte kein definiertes Basis-Layout gefunden werden.');
        return;
    }

    const breakpointKeys = Object.keys(cols) as BreakpointKey[];
    const baseIndex = breakpointOrder.indexOf(baseBreakpoint);

    for (const breakpointKey of breakpointKeys) {
        if (presetLayouts[breakpointKey].length === 0) {
            const currentBpIndex = breakpointOrder.indexOf(breakpointKey as BreakpointKey);

            const baseCols = cols[baseBreakpoint as BreakpointKey];
            const targetCols = cols[breakpointKey];

            if (currentBpIndex >= baseIndex) {
                if (baseCols !== targetCols) {
                    presetLayouts[breakpointKey] = generateFlowLayout(baseLayout, targetCols);
                } else {
                    presetLayouts[breakpointKey] = baseLayout.map((item) => ({ ...item }));
                }
            } else {
                const newCols = cols[breakpointKey];
                const newLayout = generateFlowLayout(baseLayout, newCols);
                presetLayouts[breakpointKey] = newLayout;
            }
        }
    }
}

function handleWidgetUpdate() {}

function handleWidgetDelete() {}

initializeResponsiveLayouts();

const draggable = ref(true);
const resizable = ref(true);

const layout = ref(presetLayouts.lg);

function handleKeyboardLayoutChange(event: KeyboardEvent, itemId: string) {
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
