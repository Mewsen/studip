<template>
    <div class="vote_edit">
        <label>
            {{ $gettext('Link eines Videos oder einer anderen Informationsseite (optional)') }}
            <input type="url" v-model="val_clone.url" v-autofocus ref="infoUrl"
                   @input="checkValidity()">
        </label>

        <div class="formpart">
            {{ $gettext('Hinweistext (optional)') }}
            <StudipWysiwyg v-model="val_clone.description" />
        </div>
    </div>
</template>

<script>
import { QuestionnaireComponent } from '../../mixins/QuestionnaireComponent';
import StudipWysiwyg from "../StudipWysiwyg.vue";

export default {
    name: 'questionnaire-info-edit',
    extends: QuestionnaireComponent,
    components: {StudipWysiwyg},
    created() {
        this.setDefaultValues({
            url: '',
            description: ''
        });
    },
    mounted() {
        this.checkValidity();
    },
    methods: {
        checkValidity() {
            this.$refs.infoUrl.setCustomValidity('');

            if (!this.$refs.infoUrl.checkValidity()) {
                this.$refs.infoUrl.setCustomValidity(
                    this.$gettext('Der eingegebene Link ist nicht korrekt und wird nicht angezeigt werden.')
                );
                this.$refs.infoUrl.reportValidity();
            }
        }
    }
}
</script>
