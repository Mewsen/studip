<template>
    <studip-dialog
        height="600"
        width="600"
        :title="$gettext('Seite auf OER Campus veröffentlichen')"
        :confirmText="$gettext('Veröffentlichen')"
        confirmClass="accept"
        :closeText="$gettext('Abbrechen')"
        closeClass="cancel"
        @close="showElementOerExportDialog(false)"
        @confirm="publishStructuralElement"
    >
        <template v-slot:dialogContent>
            <form v-show="!oerExportRunning" class="default" @submit.prevent="">
                <fieldset>
                    <legend>{{ $gettext('Grunddaten') }}</legend>
                    <label>
                        <p>{{ $gettext('Vorschaubild') }}:</p>
                        <img
                            v-if="structuralElement.relationships.image.data"
                            :src="structuralElement.relationships.image.meta['download-url']"
                            width="400"
                        />
                    </label>
                    <label>
                        <p>{{ $gettext('Beschreibung') }}:</p>
                        <p>{{ structuralElement.attributes.payload.description }}</p>
                    </label>
                    <label>
                        {{ $gettext('Niveau') }}:
                        <p>
                            {{ structuralElement.attributes.payload.difficulty_start }} -
                            {{ structuralElement.attributes.payload.difficulty_end }}
                        </p>
                    </label>
                    <label>
                        {{ $gettext('Lizenztyp') }}:
                        <p>{{ currentLicenseName }}</p>
                    </label>
                    <label>
                        {{ $gettext('Sie können diese Daten unter „Seiteneinstellungen“ verändern.') }}
                    </label>
                </fieldset>
                <fieldset>
                    <legend>{{ $gettext('Einstellungen') }}</legend>
                    <label>
                        {{ $gettext('Unterseiten veröffentlichen') }}
                        <input type="checkbox" v-model="oerExportChildren" />
                    </label>
                </fieldset>
            </form>
            <courseware-companion-box
                v-show="oerExportRunning"
                :msgCompanion="$gettext('Export läuft, bitte haben sie einen Moment Geduld...')"
                mood="pointing"
            />
        </template>
    </studip-dialog>
</template>

<script>
import CoursewareCompanionBox from '../layouts/CoursewareCompanionBox.vue';
import CoursewareExport from '@/vue/mixins/courseware/export.js';
import { mapActions, mapGetters } from 'vuex';

export default {
    name: 'courseware-structural-element-dialog-export-oer',
    mixins: [CoursewareExport],
    components: { CoursewareCompanionBox },
    props: {
        structuralElement: Object,
    },
    data() {
        return {
            oerExportChildren: false,
            oerExportRunning: false,
        };
    },
    computed: {
        ...mapGetters({
            context: 'context',
            licenses: 'licenses',
        }),
        currentLicenseName() {
            for (let i = 0; i < this.licenses.length; i++) {
                if (this.licenses[i]['id'] == this.structuralElement.attributes.payload.license_type) {
                    return this.licenses[i]['name'];
                }
            }

            return '';
        },
    },
    methods: {
        ...mapActions({
            showElementOerExportDialog: 'showElementOerExportDialog',
        }),

        async publishStructuralElement() {
            if (this.oerExportRunning) {
                return;
            }
            this.oerExportRunning = true;
            await this.exportToOER(this.currentElement, { withChildren: this.oerChildren });
            this.oerExportRunning = false;
            this.showElementOerDialog(false);
        },
    }
};
</script>
