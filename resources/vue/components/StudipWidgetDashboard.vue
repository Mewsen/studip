<template>
    <content-bar
        :title="title"
        :is-content-bar="true"
        icon="community"
    >
        <template #buttons-right>
            <studip-context-menu
                :title="$gettext('Hinzufügen')"
                button-shape="add"
            >
                <template #content>
                    testing...
                </template>
            </studip-context-menu>
        </template>
    </content-bar>

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
        >
            {{ item.i }}
        </GridItem>
    </GridLayout>
</template>

<script setup lang="ts">
import { ref, reactive } from "vue";

import ContentBar from "@/vue/components/ContentBar.vue";
import StudipContextMenu from "@/vue/components/StudipContextMenu.vue";

import { GridLayout, GridItem } from "grid-layout-plus";
import type { Breakpoint, Layout, LayoutItem } from "grid-layout-plus";

const props = defineProps<{
    title: string;
}>();

const breakpoints = {
    xxs: 0,
    xs: 400,
    sm: 768,
    md: 990,
    lg: 1410, // 1440px - 15px padding
    xl: 1890, // 1920px - 15px padding
    xxl: 2530, // 2560 - 15px padding
};

const cols = {
    xxs: 2,
    xs: 4,
    sm: 8,
    md: 10,
    lg: 12,
    xl: 16,
    xxl: 20,
};

type PresetLayoutMap = Record<string, LayoutItem[]>;

// Demo Data - TODO: load it from JSON:API
const presetLayouts = reactive<PresetLayoutMap>({
    xxs: [],
    xs: [],
    sm: [],
    md: [
        { x: 0, y: 0, w: 2, h: 2, i: "0" },
        { x: 2, y: 0, w: 2, h: 4, i: "1" },
        { x: 4, y: 0, w: 2, h: 5, i: "2" },
        { x: 6, y: 0, w: 2, h: 3, i: "3" },
        { x: 2, y: 4, w: 2, h: 3, i: "4" },
        { x: 4, y: 5, w: 2, h: 3, i: "5" },
        { x: 0, y: 2, w: 2, h: 5, i: "6" },
        { x: 2, y: 7, w: 2, h: 5, i: "7" },
        { x: 4, y: 8, w: 2, h: 5, i: "8" },
        { x: 6, y: 3, w: 2, h: 4, i: "9" },
        { x: 0, y: 7, w: 2, h: 4, i: "10" },
        { x: 2, y: 19, w: 2, h: 4, i: "11" },
        { x: 0, y: 14, w: 2, h: 5, i: "12" },
        { x: 2, y: 14, w: 2, h: 5, i: "13" },
        { x: 4, y: 13, w: 2, h: 4, i: "14" },
        { x: 6, y: 7, w: 2, h: 4, i: "15" },
        { x: 0, y: 19, w: 2, h: 5, i: "16" },
        { x: 8, y: 0, w: 2, h: 2, i: "17" },
        { x: 0, y: 11, w: 2, h: 3, i: "18" },
        { x: 2, y: 12, w: 2, h: 2, i: "19" },
    ],
    lg: [
        { x: 0, y: 0, w: 2, h: 2, i: "0" },
        { x: 2, y: 0, w: 2, h: 4, i: "1" },
        { x: 4, y: 0, w: 2, h: 5, i: "2" },
        { x: 6, y: 0, w: 2, h: 3, i: "3" },
        { x: 8, y: 0, w: 2, h: 3, i: "4" },
        { x: 10, y: 0, w: 2, h: 3, i: "5" },
        { x: 0, y: 5, w: 2, h: 5, i: "6" },
        { x: 2, y: 5, w: 2, h: 5, i: "7" },
        { x: 4, y: 5, w: 2, h: 5, i: "8" },
        { x: 6, y: 4, w: 2, h: 4, i: "9" },
        { x: 8, y: 4, w: 2, h: 4, i: "10" },
        { x: 10, y: 4, w: 2, h: 4, i: "11" },
        { x: 0, y: 10, w: 2, h: 5, i: "12" },
        { x: 2, y: 10, w: 2, h: 5, i: "13" },
        { x: 4, y: 8, w: 2, h: 4, i: "14" },
        { x: 6, y: 8, w: 2, h: 4, i: "15" },
        { x: 8, y: 10, w: 2, h: 5, i: "16" },
        { x: 10, y: 4, w: 2, h: 2, i: "17" },
        { x: 0, y: 9, w: 2, h: 3, i: "18" },
        { x: 2, y: 6, w: 2, h: 2, i: "19" },
    ],
    xl: [],
    xxl: []
});



function breakpointChangedEvent(
    newBreakpoint: Breakpoint,
    newLayout: Layout
) {
    console.info(
        "BREAKPOINT CHANGED breakpoint=",
        newBreakpoint,
        ", layout: ",
        newLayout
    );
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
            // h: Höhe bleibt unverändert
        });

        currentX += newW;
        maxHInCurrentRow = Math.max(maxHInCurrentRow, item.h);
    });

    return newLayout;
};


function initializeResponsiveLayouts() {
    
    const breakpointOrder = ["xxs", "xs", "sm", "md", "lg", "xl", "xxl"];
    let baseLayout: Layout | null = null;
    let baseBreakpoint: string | null = null;

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
        console.error("FEHLER: Es konnte kein definiertes Basis-Layout gefunden werden.");
        return;
    }

    const breakpointKeys = Object.keys(cols) as BreakpointKey[];
    const baseIndex = breakpointOrder.indexOf(baseBreakpoint);


    for (const breakpointKey of breakpointKeys) {
        
        if (presetLayouts[breakpointKey].length === 0) {
            
            const currentBpIndex = breakpointOrder.indexOf(breakpointKey as string);
            
            const baseCols = cols[baseBreakpoint as BreakpointKey]; 
            const targetCols = cols[breakpointKey];

            if (currentBpIndex >= baseIndex) {
                if (baseCols !== targetCols) {
                    presetLayouts[breakpointKey] = generateFlowLayout(baseLayout, targetCols);
                } else {
                     presetLayouts[breakpointKey] = baseLayout.map(item => ({...item}));
                }
            } 
            else {
                const newCols = cols[breakpointKey];
                const newLayout = generateFlowLayout(baseLayout, newCols);
                presetLayouts[breakpointKey] = newLayout;
            }
        }
    }
}

initializeResponsiveLayouts();

const draggable = ref(true);
const resizable = ref(true);

const layout = ref(presetLayouts.lg);
</script>
