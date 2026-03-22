<template>
    <div class="vote_edit">
        <label>
            {{ $gettext('Überschrift') }}
            <input type="text" v-model="val_clone.description">
        </label>
    </div>
</template>

<script>
import { QuestionnaireComponent } from '../../mixins/QuestionnaireComponent';

export default {
    name: 'headline-edit',
    extends: QuestionnaireComponent,
    created() {
        this.setDefaultValues({
            description: ''
        });
    }
}
</script>
