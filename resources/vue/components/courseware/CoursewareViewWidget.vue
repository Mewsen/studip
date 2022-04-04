<template>
    <ul class="widget-list widget-links sidebar-views cw-view-widget">
        <li :class="{ active: readView }">
            <button @click="setReadView">
                <translate>Lesen</translate>
            </button>
        </li>
        <li :class="{ active: editView }">
            <button @click="setEditView">
                <translate>Bearbeiten</translate>
            </button>
        </li>
    </ul>
</template>

<script>
import { mapActions } from 'vuex';

export default {
    name: 'courseware-view-widget',
    computed: {
        readView() {
            return this.$store.getters.viewMode === 'read';
        },
        editView() {
            return this.$store.getters.viewMode === 'edit';
        },
    },
    methods: {
        ...mapActions({
            coursewareViewMode: 'coursewareViewMode',
            coursewareBlockAdder: 'coursewareBlockAdder',
            setToolbarItem: 'coursewareSelectedToolbarItem',
        }),
        setReadView() {
            this.coursewareViewMode('read');
            this.setToolbarItem('contents');
            this.coursewareBlockAdder({});
        },
        setEditView() {
            this.coursewareViewMode('edit');
        },
    },
};
</script>
