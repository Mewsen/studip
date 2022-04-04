<template>
    <ul class="widget-list widget-links cw-export-widget" v-if="structuralElement">
        <li class="cw-export-widget-export">
            <button @click="exportElement">
                <translate>Seite exportieren</translate>
            </button>
        </li>
        <li v-if="oerEnabled" class="cw-export-widget-oer">
            <button @click="oerElement">
                <translate>Seite auf %{oerTitle} veröffentlichen</translate>
            </button>
        </li>
    </ul>
</template>

<script>
import CoursewareExport from '@/vue/mixins/courseware/export.js';
import { mapActions, mapGetters } from 'vuex';

export default {
    name: 'courseware-export-widget',
    props: ['structuralElement', 'canVisit'],
    mixins: [CoursewareExport],
    computed: {
        ...mapGetters({
            context: 'context',
            oerEnabled: 'oerEnabled',
            oerTitle: 'oerTitle',
        }),
    },
    methods: {
        ...mapActions({
            showElementExportDialog: 'showElementExportDialog',
            showElementOerDialog: 'showElementOerDialog',
        }),
        exportElement() {
            this.showElementExportDialog(true);
        },
        oerElement() {
            this.showElementOerDialog(true);
        }
    },
};
</script>
