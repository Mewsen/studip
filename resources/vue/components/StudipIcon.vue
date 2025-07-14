<template>
    <div
        v-if="!name"
        v-bind="$attrs"
        v-html="svgContent"
        :role="ariaRole"
        :class="cssClass"
        :style="computedStyle"
    />

    <label v-else class="icon-button undecorated">
        <input type="submit" hidden v-bind="$attrs">
        <div v-html="svgContent" :role="ariaRole" :class="cssClass" :style="computedStyle" />
        <span v-if="text">{{ text }}</span>
    </label>
</template>

<script lang="ts">
import { defineComponent } from 'vue';
import iconLoader from "../../assets/javascripts/lib/icon-loader";

export default defineComponent({
    name: 'studip-icon',
    props: {
        ariaRole: { type: String, required: false },
        name: { type: String, required: false },
        role: { type: String, required: false, default: 'clickable' },
        shape: { type: String, required: true },
        size: { type: Number, required: false, default: null },
        inline: { type: Boolean, default: false },
        text: { type: String, required: false }
    },
    data() {
        return { svgContent: '' };
    },
    computed: {
        color(): string {
            const roleColors: Record<string, string> = {
                accept: 'green',
                attention: 'red',
                clickable: 'blue',
                info: 'black',
                info_alt: 'white',
                inactive: 'grey',
                navigation: 'blue',
                new: 'red',
                sort: 'blue',
                'status-green': 'green',
                'status-red': 'red',
                'status-yellow': 'yellow',
            };

            return roleColors[this.role] ?? 'blue';
        },
        cssClass(): string[] {
            return [
                'studip-icon',
                this.inline ? 'studip-icon-inline' : '',
                `icon-role-${this.role}`,
                `icon-shape-${this.shape}`
            ];
        },
        computedStyle(): Record<string, string> {
            return this.size
                ? { width: `${this.size}px`, height: `${this.size}px` }
                : {}; // Falls size nicht gesetzt ist, greift CSS mit --icon-size-default
        }
    },
    watch: {
        shape: {
            immediate: true,
            handler(shape) {
                iconLoader.load(shape).then((svg: string) => {
                    this.svgContent = svg;
                });
            }
        }
    }
});
</script>
