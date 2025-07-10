<template>
  <ul class="widget-list widget-links sidebar-views cw-view-widget">
        <li :class="{ active: templatesView }">
            <a href="#" @click.prevent="setTemplatesView">
                {{ $gettext('Vorlagen') }}
            </a>
        </li>
        <li>
            <a :href="blockTypeUrl">
                {{ $gettext("Block-Typen") }}
            </a>
        </li>
        <li>
            <a :href="containerTypeUrl">
                {{ $gettext("Container-Typen") }}
            </a>
        </li>
  </ul>
</template>

<script>
import { mapGetters, mapActions } from 'vuex';

export default {
    name: 'courseware-admin-view-widget',
    computed: {
        ...mapGetters({
            adminViewMode: 'adminViewMode'
        }),
        blockTypeUrl() {
            return window.STUDIP.URLHelper.getURL('dispatch.php/admin/courseware/block_types');
        },
        containerTypeUrl() {
            return window.STUDIP.URLHelper.getURL('dispatch.php/admin/courseware/container_types');
        },
        templatesView() {
            return this.adminViewMode === 'templates';
        },
    },
    methods: {
        ...mapActions({
            setAdminViewMode: 'adminViewMode'
        }),
        setTemplatesView() {
            this.setAdminViewMode('templates');
        },
    }
}
</script>
