<template>
    <div>
        <div class="formpart" tabindex="0">
            {{ $gettext('Frage') }}
            <StudipWysiwyg v-model="val_clone.description" />
        </div>

        <label>
            <input type="checkbox" v-model="val_clone.mandatory">
            {{ $gettext('Pflichtfrage') }}
        </label>
    </div>
</template>

<script>
import { QuestionnaireComponent } from '../../mixins/QuestionnaireComponent';
import StudipWysiwyg from "../StudipWysiwyg.vue";

export default {
    extends: QuestionnaireComponent,
    name: 'freetext-edit',
    components: {StudipWysiwyg},
    created() {
        this.setDefaultValues({
            description: '',
            mandatory: '0',
        });
    }
}
</script>
