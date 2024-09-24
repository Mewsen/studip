<template>
    <div>
        <div class="formpart" tabindex="0" ref="autofocus">
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

export default {
    name: 'freetext-edit',
    mixins: [ QuestionnaireComponent ],
    created() {
        this.setDefaultValues({
            description: '',
            mandatory: '0',
        });
    },
    mounted() {
        this.$refs.autofocus.focus();
    }
}
</script>
