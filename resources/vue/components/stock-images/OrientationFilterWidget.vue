<template>
    <SidebarWidget :title="$gettext('Seitenausrichtung')">
        <template #content>
            <label>
                <span class="sr-only">{{ $gettext('Wählen Sie eine Seitenausrichtung') }}</span>
                <select v-model="orientation" class="sidebar-selectlist">
                    <option v-for="[value, { text }] in Object.entries(orientations)" :value="value" :key="value">
                        {{ text }}
                    </option>
                </select>
            </label>
        </template>
    </SidebarWidget>
</template>
<script>
import SidebarWidget from '../SidebarWidget.vue';
import { orientations } from './filters.js';

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
    computed: {
        orientation: {
            get() {
                return this.filters.orientation;
            },
            set(orientation) {
                this.$emit('update:filters', { ...this.filters, orientation });
            }
        },
        orientations() {
            return orientations;
        },
    },
};
</script>
