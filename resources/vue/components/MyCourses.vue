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

        <Teleport to="#tiled-courses-sidebar-switch .sidebar-widget-content .widget-list" name="sidebar-switch">
            <MyCoursesSidebarSwitch />
        </Teleport>

        <Teleport to="#tiled-courses-new-contents-toggle .sidebar-widget-content .widget-list" name="sidebar-content-toggle">
            <MyCoursesNewContentToggle />
        </Teleport>
    </div>
</template>

<script>
import MyCoursesTables from './MyCoursesTables.vue';
import MyCoursesTiles from './MyCoursesTiles.vue';
import MyCoursesMixin from '../mixins/MyCoursesMixin.js';
import MyCoursesSidebarSwitch from "./MyCoursesSidebarSwitch.vue";
import MyCoursesNewContentToggle from "./MyCoursesNewContentToggle.vue";

export default {
    name: 'MyCourses',
    mixins: [MyCoursesMixin],
    components: {
        MyCoursesTables,
        MyCoursesTiles,
        MyCoursesSidebarSwitch,
        MyCoursesNewContentToggle,
    },
    computed: {
        displayComponent () {
            return this.displayedType === 'tiles'
                 ? MyCoursesTiles
                 : MyCoursesTables;
        },
        displayedType () {
            return this.getViewConfig('tiled') ? 'tiles' : 'table';
        },
        iconSize () {
            if (this.displayedType !== 'tiles' && !this.responsiveDisplay) {
                return 20;
            }
            return 24;
        },
        searchCoursesUrl () {
            return STUDIP.URLHelper.getURL('dispatch.php/search/courses');
        },
        isEmpty () {
            return this.groups.length === 0;
        }
    },
    beforeMount() {
        document.querySelector('#tiled-courses-sidebar-switch .widget-list').innerHTML = '';
        document.querySelector('#tiled-courses-new-contents-toggle .widget-list').innerHTML = '';
    }
}
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
