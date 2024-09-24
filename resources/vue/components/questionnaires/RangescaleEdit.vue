<template>
    <div class="rangescale_edit">

        <div class="formpart" tabindex="0" ref="autofocus">
            {{ $gettext('Einleitungstext') }}
            <StudipWysiwyg v-model="val_clone.description" />
        </div>

        <InputArray v-model="val_clone.statements"
                     :label="$gettext('Aussage')"
                     :label-plural="$gettext('Aussagen')"
                     :additional-colspan="options.length"
        >
             <template #header-cells>
                 <th v-for="(option, index) in options" class="option-cell" :key="index">
                     {{ option }}
                 </th>
             </template>
            <template #body-cells>
                <td v-for="(option, index) in options" class="option-cell" :key="index">
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

        <label>
            {{ $gettext('Maximum') }}
            <input type="number" v-model.number="val_clone.maximum" :min="val_clone.minimum">
        </label>

        <label>
            {{ $gettext('Minimum') }}
            <input type="number" v-model.number="val_clone.minimum" min="1" :max="val_clone.maximum">
        </label>

        <label>
            {{ $gettext('Ausweichantwort (leer lassen für keine)') }}
            <input type="text" v-model.trim="val_clone.alternative_answer">
        </label>
    </div>
</template>

<script>
import InputArray from './InputArray.vue';
import { QuestionnaireComponent } from '../../mixins/QuestionnaireComponent';

export default {
    name: 'rangescale-edit',
    components: { InputArray },
    mixins: [ QuestionnaireComponent ],
    created() {
        this.setDefaultValues({
            alternative_answer: '',
            description: '',
            mandatory: 0,
            maximum: 5,
            minimum: 1,
            randomize: 0,
            statements: ['', '', '', '']
        });
    },
    mounted() {
        this.$refs.autofocus.focus();
    },
    computed: {
        options() {
            let result = [];
            for (let i = this.val_clone.minimum; i <= this.val_clone.maximum; i += 1) {
                result.push(i);
            }
            if (this.val_clone.alternative_answer.length > 0) {
                result.push(this.val_clone.alternative_answer);
            }
            return result;
        }
    }
}
</script>
