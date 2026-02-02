<template>
    <div><label>
        {{ $gettext('Fügt eine horizontale Trennlinie ein.') }}
        </label>
    </div>
</template>

<script>
import { QuestionnaireComponent } from '../../mixins/QuestionnaireComponent';

export default {
    name: 'divider-edit',
    extends: QuestionnaireComponent,
    created() {
        this.setDefaultValues({
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
