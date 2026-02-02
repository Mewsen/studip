<template>

</template>

<script>
import { QuestionnaireComponent } from '../../mixins/QuestionnaireComponent';
import StudipWysiwyg from "../StudipWysiwyg.vue";

export default {
    name: 'divider-edit',
    extends: QuestionnaireComponent,
    components: {StudipWysiwyg},
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
