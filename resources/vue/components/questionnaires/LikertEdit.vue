<template>
    <div class="likert_edit">
        <div class="formpart" tabindex="0" ref="autofocus">
            {{ $gettext('Einleitungstext' )}}
            <StudipWysiwyg v-model="val_clone.description" />
        </div>

        <InputArray v-model="val_clone.statements"
                     :label="$gettext('Aussage')"
                     :label-plural="$gettext('Aussagen')"
                     :additional-colspan="val_clone.options.length"
        >
            <template #header-cells>
                <th v-for="(option, index) in val_clone.options" class="option-cell" :key="index">
                    {{ option }}
                </th>
            </template>

            <template #body-cells>
                <td v-for="(option, index) in val_clone.options" class="option-cell" :key="index">
                    <input type="radio" disabled :title="option">
                </td>
            </template>
        </InputArray>

        <label>
            <input type="checkbox" v-model.number="val_clone.mandatory" true-value="1" false-value="0">
            {{ $gettext('Pflichtfrage') }}
        </label>
        <label>
            <input type="checkbox" v-model.number="val_clone.randomize" true-value="1" false-value="0">
            {{ $gettext('Antworten den Teilnehmenden zufällig präsentieren') }}
        </label>

        <div>
            <div>{{ $gettext('Antwortmöglichkeiten konfigurieren') }}</div>
            <InputArray v-model="val_clone.options" />
        </div>
    </div>
</template>

<script>
import { $gettext } from '../../../assets/javascripts/lib/gettext';
import InputArray from "./InputArray.vue";
import StudipWysiwyg from '../StudipWysiwyg.vue';
import { QuestionnaireComponent } from '../../mixins/QuestionnaireComponent';

// This is necesssar since $gettext does not seem to work in data() or created()
const default_values = () => ({
    description: '',
    statements: ['', '', '', ''],
    mandatory: 0,
    randomize: 0,
    options: [
        $gettext('trifft zu'),
        $gettext('trifft eher zu'),
        $gettext('teils-teils'),
        $gettext('trifft eher nicht zu'),
        $gettext('trifft nicht zu'),
    ],
});

export default {
    name: 'likert-edit',
    components: { StudipWysiwyg, InputArray },
    mixins: [ QuestionnaireComponent ],
    created() {
        this.setDefaultValues(default_values());
    },
    mounted() {
        this.$refs.autofocus.focus();
    }
}
</script>
