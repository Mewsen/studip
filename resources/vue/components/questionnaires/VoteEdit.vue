<template>
    <div class="vote_edit">
        <div class="formpart" tabindex="0" ref="autofocus">
            {{ $gettext('Frage') }}
            <StudipWysiwyg v-model="val_clone.description" />
        </div>

        <InputArray v-model="val_clone.options" />

        <label>
            <input type="checkbox" v-model.number="val_clone.multiplechoice" true-value="1" false-value="0">
            {{ $gettext('Mehrere Antworten sind erlaubt') }}
        </label>
        <label>
            <input type="checkbox" v-model.number="val_clone.mandatory" true-value="1" false-value="0">
            {{ $gettext('Pflichtfrage') }}
        </label>
        <label>
            <input type="checkbox" v-model.number="val_clone.randomize" true-value="1" false-value="0">
            {{ $gettext('Antworten den Teilnehmenden zufällig präsentieren') }}
        </label>

    </div>
</template>

<script>
import InputArray from "./InputArray.vue";
import { QuestionnaireComponent } from '../../mixins/QuestionnaireComponent';
import StudipWysiwyg from '../StudipWysiwyg.vue';

export default {
    name: 'vote-edit',
    components: { StudipWysiwyg, InputArray },
    mixins: [QuestionnaireComponent],
    created() {
        this.setDefaultValues({
            description: '',
            mandatory: '0',
            multiplechoice: '1',
            options: ['', '', '', ''],
            randomize: '0',
        });
    },
    mounted() {
        this.$refs.autofocus.focus();
    }
}
</script>
