<template>
    <div id="mycourses">
        <studip-message-box v-if="isEmpty" type="info" :hideClose="true">
            {{ $gettext('Es wurden keine Veranstaltungen gefunden.') }}
            {{ $gettext('Mögliche Ursachen:') }}
            <template #details>
                <ul>
                    <li v-translate>
                        Sie haben zur Zeit keine Veranstaltungen belegt, an denen Sie teilnehmen können.
                        <br>
                        Bitte nutzen Sie <a :href="searchCoursesUrl"> <strong>Veranstaltung suchen / hinzufügen</strong> </a> um sich für Veranstaltungen anzumelden.
                    </li>

                    <li v-translate>
                        In dem ausgewählten <strong>Semester</strong> wurden keine Veranstaltungen belegt.
                        <br>
                        Wählen Sie links im <strong>Semesterfilter</strong> ein anderes Semester aus!
                    </li>
                </ul>
            </template>
        </studip-message-box>
        <component v-else :is="displayComponent" :icon-size="iconSize"></component>

        <Teleport
            v-if="hasSidebarElements"
            to="#tiled-courses-sidebar-switch .sidebar-widget-content .widget-list"
            name="sidebar-switch"
        >
            <SidebarSwitch />
        </Teleport>

        <Teleport
            v-if="hasSidebarElements"
            to="#tiled-courses-new-contents-toggle .sidebar-widget-content .widget-list"
            name="sidebar-content-toggle"
        >
            <NewContentToggle />
        </Teleport>
    </div>
</template>

<script>
import TableView from '@/vue/components/my-courses/TableView.vue';
import TileView from '@/vue/components/my-courses/TileView.vue';
import MyCoursesMixin from '@/vue/mixins/MyCoursesMixin.js';
import SidebarSwitch from '@/vue/components/my-courses/SidebarSwitch.vue';
import NewContentToggle from '@/vue/components/my-courses/NewContentToggle.vue';

export default {
    name: 'MyCourses',
    mixins: [MyCoursesMixin],
    components: {
        TableView,
        TileView,
        SidebarSwitch,
        NewContentToggle,
    },
    data() {
        return {
            hasSidebarElements: true,
        };
    },
    computed: {
        displayComponent() {
            return this.displayedType === 'tiles' ? TileView : TableView;
        },
        displayedType() {
            return this.getViewConfig('tiled') ? 'tiles' : 'table';
        },
        iconSize() {
            if (this.displayedType !== 'tiles' && !this.responsiveDisplay) {
                return 20;
            }
            return 24;
        },
        searchCoursesUrl() {
            return STUDIP.URLHelper.getURL('dispatch.php/search/courses');
        },
        isEmpty() {
            return this.groups.length === 0;
        },
    },
    beforeMount() {
        ['#tiled-courses-sidebar-switch .widget-list', '#tiled-courses-new-contents-toggle .widget-list'].forEach(
            (selector) => {
                const element = document.querySelector(selector);
                if (element) {
                    element.innerHTML = '';
                } else {
                    this.hasSidebarElements = false;
                }
            },
        );
    },
};
</script>

<style lang="scss">
.course-hidden-info {
    white-space: nowrap;

    img,
    svg {
        vertical-align: text-bottom;
    }
}
</style>
