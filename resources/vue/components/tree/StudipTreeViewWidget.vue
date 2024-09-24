<template>
    <sidebar-widget id="views-widget" class="sidebar-views" :title="$gettext('Ansicht')">
        <template #content>
            <ul class="widget-list widget-links sidebar-views">
                <li :class="{ active: config.view === 'list' }">
                    <a :href="getUrl('list')"
                       :title="$gettext('Verzeichnis als Liste anzeigen')"
                       tabindex="0">
                        {{ $gettext('Als Liste anzeigen') }}
                    </a>
                </li>
                <li :class="{ active: config.view === 'table' }">
                    <a :href="getUrl('table')"
                       :title="$gettext('Verzeichnis als Tabelle anzeigen')"
                       tabindex="0">
                        {{ $gettext('Als Tabelle anzeigen') }}
                    </a>
                </li>
            </ul>
        </template>
    </sidebar-widget>
</template>

<script>
import SidebarWidget from '../SidebarWidget.vue';

export default {
    name: 'StudipTreeViewWidget',
    components: {
        SidebarWidget
    },
    props: {
        config: {
            type: Object,
            required: true
        }
    },
    methods: {
        getUrl(showAs) {
            const url = new URL(window.location);
            url.searchParams.set('show_as', showAs);
            url.searchParams.set('node_id', this.config.node.id);

            if (this.config.semester !== '') {
                url.searchParams.set('semester', this.config.semester);
            }

            if (this.config.semClass !== 0) {
                url.searchParams.set('semclass', this.config.semClass);
            }

            return url.toString();
        }
    }
}
</script>
