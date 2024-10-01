<template>
    <StudipProgressIndicator v-if="loadingUnits" :description="$gettext('Vorgang wird bearbeitet...')" />
    <ul v-else class="cw-ribbon-tools-units">
        <li v-for="unit in units" :key="unit.id">
            <CoursewareToolsUnitsItem :unit="unit" :element="getUnitElement(unit)" />
        </li>
        <li v-if="emptyUnits">
            <CoursewareCompanionBox mood="sad" :msgCompanion="emptyUnitsMessage" :border="false"/>
        </li>
    </ul>
</template>

<script>
import CoursewareToolsUnitsItem from './CoursewareToolsUnitsItem.vue';
import CoursewareCompanionBox from '../layouts/CoursewareCompanionBox.vue';
import StudipProgressIndicator from '../../StudipProgressIndicator.vue';
import { mapActions, mapGetters } from 'vuex';
export default {
    name: 'CoursewareToolsUnits',
    components: {
        CoursewareToolsUnitsItem,
        CoursewareCompanionBox,
        StudipProgressIndicator,
    },
    data() {
        return {
            loadingUnits: false,
        };
    },
    computed: {
        ...mapGetters({
            context: 'context',
            coursewareUnits: 'courseware-units/all',
            currentUnit: 'currentUnit',
            elementById: 'courseware-structural-elements/byId',
            userId: 'userId',
        }),
        units() {
            return (
                this.coursewareUnits
                    .filter(
                        (unit) =>
                            unit.relationships.range.data.id === this.context.id && unit.id !== this.currentUnit.id
                    )
                    .sort((a, b) => a.attributes.position - b.attributes.position) ?? []
            );
        },
        inCourseContext() {
            return  this.context.type === 'courses';
        },
        inUserContext() {
            return this.context.type === 'users';
        },
        emptyUnits() {
            return this.units.length === 0;
        },
        emptyUnitsMessage() {
            if (this.inCourseContext) {
                return this.$gettext('Es wurden keine weiteren Lernmaterialien in dieser Veranstaltung gefunden.');
            }
            if (this.inUserContext) {
                return this.$gettext('Es wurden keine weiteren Lernmaterialien gefunden.');
            }

            return '';
        }
    },
    methods: {
        ...mapActions({
            loadCourseUnits: 'loadCourseUnits',
            loadUserUnits: 'loadUserUnits',
        }),
        getUnitElement(unit) {
            const elementId = unit.relationships['structural-element'].data.id;
            return this.elementById({ id: elementId });
        },
    },
    async beforeMount() {
        if (this.coursewareUnits.length === 0) {
            this.loadingUnits = true;
        }

        if (this.inCourseContext) {
            await this.loadCourseUnits(this.context.id);
        }
        if (this.inUserContext) {
            await this.loadUserUnits(this.userId);
        }

        this.loadingUnits = false;
    },
};
</script>
<style lang="scss">
.cw-ribbon-tools-units {
    list-style: none;
    padding: 0;

    li {
        margin-bottom: 2em;
    }
}
</style>
