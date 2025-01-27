<template>
    <ul class="widget-list widget-links cw-action-widget">
        <li v-if="templatesView" class="cw-action-widget-add">
            <a href="#"  @click.prevent="addTemplate">
                {{ $gettext('Vorlage hinzufügen') }}
            </a>
        </li>
    </ul>
</template>

<script>
import { mapGetters, mapActions } from 'vuex';

export default {
    name: 'courseware-admin-action-widget',
    computed: {
        ...mapGetters({
            adminViewMode: 'adminViewMode',
            showAddTemplateDialog: 'showAddTemplateDialog'
        }),
        templatesView() {
            return this.adminViewMode === 'templates';
        }
    },
    methods: {
        ...mapActions({
            setShowAddTemplateDialog: 'showAddTemplateDialog'
        }),
        addTemplate() {
            this.setShowAddTemplateDialog(true);
        }
    }
}
</script>
