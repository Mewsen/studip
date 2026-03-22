<template>
    <div><label>
        {{ $gettext('Fügt einen Seitenumbruch ein.') }}
    </label></div>
</template>

<script>
import { QuestionnaireComponent } from '../../mixins/QuestionnaireComponent';

export default {
    name: 'pagebreak-edit',
    extends: QuestionnaireComponent,
    created() {
        this.setDefaultValues({
        });
    }
}
</script>
