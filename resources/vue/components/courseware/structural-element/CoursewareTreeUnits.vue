<template>
    <div v-if="canEditRoot || linkedUnits.length > 0" class="cw-tree-units">
        <div class="cw-tree-unit-title">{{ $gettext('Weitere Lernmaterialien') }}</div>
        <div v-if="!processing">
            <ol v-if="linkedUnits.length > 0">
                <courseware-tree-unit
                    v-for="unit in linkedUnits"
                    :unit="unit"
                    :canEditRoot="canEditRoot"
                    :key="unit.id"
                    @removeUnitLink="removeUnitLink"
                ></courseware-tree-unit>
            </ol>
            <div v-if="canEditRoot && units.length > 0" class="cw-tree-units-adder">
                <form v-if="showForm" class="default cw-tree-units-adder-form" @submit.prevent="">
                    <label>
                        <span class="sr-only">{{ $gettext('Lernmaterial') }}</span>
                        <select v-model="selectedUnit" name="addUnit" @change="addUnitLink">
                            <option v-show="false" value="" disabled>
                                {{ $gettext('Link zum Lernmaterial auswählen') }}
                            </option>
                            <option v-for="(unit, index) in units" :key="index" :value="unit.id">
                                {{ getUnitTitle(unit) }}
                            </option>
                        </select>
                    </label>
                    <button
                        v-if="canEditRoot"
                        class="button cancel"
                        :title="$gettext('Auswahl abbrechen')"
                        @click.prevent="showForm = false"
                    ></button>
                </form>
                <button
                    v-else
                    class="add-element"
                    :title="$gettext('Link zum Lernmaterial hinzufügen')"
                    @click="showForm = true"
                >
                    <studip-icon shape="add" />
                </button>
            </div>
        </div>
        <studip-progress-indicator v-else :description="$gettext('Vorgang wird bearbeitet...')" />
    </div>
</template>

<script>
import CoursewareTreeUnit from './CoursewareTreeUnit.vue';
import StudipProgressIndicator from '../../StudipProgressIndicator.vue';
import colorMixin from '@/vue/mixins/courseware/colors.js';
import { mapActions, mapGetters } from 'vuex';

export default {
    name: 'CoursewareTreeUnits',
    mixins: [colorMixin],
    components: {
        CoursewareTreeUnit,
        StudipProgressIndicator,
    },
    data() {
        return {
            processing: false,
            showForm: false,
            selectedUnit: '',
            currentInstance: null,
            identimage: '',
        };
    },
    computed: {
        ...mapGetters({
            context: 'context',
            courseUnits: 'courseware-units/all',
            currentRootElement: 'currentRootElement',
            unitById: 'courseware-units/byId',
            instanceById: 'courseware-instances/byId',
            structuralElementById: 'courseware-structural-elements/byId',
        }),

        instance() {
            if (this.context.type === 'courses') {
                return this.instanceById({ id: 'course_' + this.context.id + '_' + this.context.unit });
            } else {
                return this.instanceById({ id: 'user_' + this.context.id + '_' + this.context.unit });
            }
        },

        canEditRoot() {
            return this.currentRootElement?.attributes['can-edit'];
        },

        units() {
            // returns all course units that are not already linked
            const units = this.courseUnits;
            const unitsWithoutSelf = units.filter((unit) => unit.id !== this.context.unit);
            const linkedUnits = this.currentInstance?.attributes['linked-units'];
            if (linkedUnits) {
                return unitsWithoutSelf.filter((unit) => !this.instance.attributes['linked-units'].includes(unit.id));
            } else {
                return unitsWithoutSelf;
            }
        },

        linkedUnits() {
            // returns the required unit data of all linked units
            const units = this.courseUnits;
            const linkedUnitIds = this.currentInstance?.attributes['linked-units'];

            if (linkedUnitIds) {
                // filter out not linked units
                const filteredUnits = units.filter((unit) =>
                    this.instance.attributes['linked-units'].includes(unit.id)
                );
                // map units to their unit ids instead of array keys to return the correct order
                const mappedUnits = new Map(filteredUnits.map((unit) => [unit.id, unit]));

                return linkedUnitIds.map((unit) => mappedUnits.get(unit));
            }
            return [];
        },
    },

    mounted() {
        this.initData();
    },

    methods: {
        ...mapActions({
            loadCourseUnits: 'loadCourseUnits',
            storeCoursewareLinkedUnits: 'storeCoursewareLinkedUnits',
        }),

        async initData() {
            if (this.context.type === 'courses') {
                this.currentInstance = this.instance;
                const linkedUnits = this.currentInstance?.attributes['linked-units'];
                if (this.canEditRoot || linkedUnits.length > 0) {
                    this.processing = true;
                    await this.loadCourseUnits(this.context.id);
                    this.processing = false;
                }
            }
        },

        getUnitTitle(unit) {
            const rootElement = this.structuralElementById({
                id: unit.relationships['structural-element'].data.id,
            });
            return rootElement.attributes.title;
        },

        async addUnitLink() {
            this.showForm = false;
            this.processing = true;
            const linkedUnits = this.currentInstance.attributes['linked-units'];
            if (!linkedUnits) {
                await this.storeCoursewareLinkedUnits({
                    instance: this.currentInstance,
                    linkedUnits: [this.selectedUnit],
                });
            } else if (!linkedUnits.includes(this.selectedUnit)) {
                this.currentInstance.attributes['linked-units'].push(this.selectedUnit);
                await this.storeCoursewareLinkedUnits({
                    instance: this.currentInstance,
                    linkedUnits: linkedUnits,
                });
            }
            this.processing = false;
        },

        async removeUnitLink(id) {
            let linkedUnits = this.currentInstance.attributes['linked-units'].filter((unitId) => unitId !== id);
            await this.storeCoursewareLinkedUnits({
                instance: this.currentInstance,
                linkedUnits: linkedUnits,
            });
            this.selectedUnit = '';
        },
    },
};
</script>
<style lang="scss">
.cw-tree-units {
    .progress-indicator-wrapper {
        margin-top: 15px;
    }
}
</style>