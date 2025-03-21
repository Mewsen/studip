<template>
    <sidebar-widget id="views-widget" class="sidebar-views" :title="$gettext('Ansicht')">
        <template #content>
            <ul class="widget-list widget-links sidebar-views">
                <li :class="{ active: viewType === 'list' }">
                    <a :href="getUrl('list')"
                       :title="$gettext('Verzeichnis als Liste anzeigen')"
                       tabindex="0"
                       @click.prevent="setType('list')"
                    >
                        {{ $gettext('Als Liste anzeigen') }}
                    </a>
                </li>
                <li :class="{ active: viewType === 'table' }">
                    <a :href="getUrl('table')"
                       :title="$gettext('Verzeichnis als Tabelle anzeigen')"
                       tabindex="0"
                       @click.prevent="setType('table')"
                    >
                        {{ $gettext('Als Tabelle anzeigen') }}
                    </a>
                </li>
            </ul>
        </template>
    </sidebar-widget>
</template>

<script>
import SidebarWidget from '../SidebarWidget.vue';
import {mapMutations, mapState} from "vuex";

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
    computed: {
        ...mapState('treestore', [
            'viewType',
        ]),
    },
    methods: {
        ...mapMutations('treestore', {
            setViewType: 'SET_VIEW_TYPE',
        }),
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
        },
        setType(type) {
            this.setViewType(type);
        }
    }
}
</script>
