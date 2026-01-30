<template>
    <div class="vote_edit">
        <label>
            {{ $gettext('Überschrift') }}
            <input type="text" v-model="val_clone.url">
        </label>
    </div>
</template>

<script>
import { QuestionnaireComponent } from '../../mixins/QuestionnaireComponent';
import StudipWysiwyg from "../StudipWysiwyg.vue";

export default {
    name: 'headline-edit',
    extends: QuestionnaireComponent,
    components: {StudipWysiwyg},
    created() {
        this.setDefaultValues({
            headline: ''
        });
    },
    mounted() {
        this.checkValidity();
    },
    methods: {
        checkValidity() {

        }
    }
}
</script>
