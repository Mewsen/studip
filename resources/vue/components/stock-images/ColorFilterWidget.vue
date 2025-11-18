<template>
    <SidebarWidget :title="$gettext('Farbe')">
        <template #content>
            <StudipSelect
                multiple
                v-model="selectedColors"
                :options="selectableColors"
                @input="onVueSelectInput"
                label="name"
            >
                <template #option="option">
                    <span class="vs__option-color" :style="{ 'background-color': option.hex }"></span>
                    <span>{{ option.name }}</span>
                </template>

                <template #selected-option="option">
                    <span class="vs__option-color" :style="{ 'background-color': option.hex }" :title="name"></span>
                </template>

                <template #no-options>{{ $gettext('Keine Auswahlmöglichkeiten') }}</template>
            </StudipSelect>
        </template>
    </SidebarWidget>
</template>
<script>
import { colors as selectableColors } from './colors.js';
import SidebarWidget from '../SidebarWidget.vue';

export default {
    emits: ['update:filters'],
    props: {
        filters: {
            type: Object,
            required: true,
        },
    },
    components: {
        SidebarWidget,
    },
    data: () => ({
        selectedColors: [],
    }),
    computed: {
        selectableColors: () => selectableColors,
    },
    methods: {
        onVueSelectInput(selectedColors) {
            const colors = selectedColors.map(({ hex }) => hex);
            this.$emit('update:filters', { ...this.filters, colors });
        },
    },
    mounted() {
        this.selectedColors = this.selectableColors.filter(({ hex }) => this.filters.colors.includes(hex));
    },
    watch: {
        filters: {
            handler() {
                this.selectedColors = this.selectableColors.filter(({ hex }) => this.filters.colors.includes(hex));
            },
            deep: true,
        },
    },
};
</script>

<!-- <style scoped>
.stock-images-filters-color-swatch {
    box-shadow: 0 0 0 1px var(--base-color-20);
    box-sizing: border-box;
    display: inline-block;
    width: 20px;
    height: 20px;
    transition: all 0.1s;
}
</style> -->
