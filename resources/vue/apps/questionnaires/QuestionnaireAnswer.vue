<template>
    Hallo, ich bin ein Template!

    {{ questionnaireData.title }}


    <div class="questionnaire_answer">
        <article v-for="question in questionnaireData.questions">
        {{ question.component }}

            {{ question.questiontype }}

        </article>

    </div>

    {{ questionnaireData.anonymous }}

    <div class="terms">
        <span v-if="questionnaireData.anonymous == 1 ">{{ $gettext('Die Teilnahme ist anonym.') }}</span>
        <span v-else>{{ $gettext('Die Teilnahme ist nicht anonym.') }}</span>
    </div>
</template>

<script >
import {$gettext} from "../../../assets/javascripts/lib/gettext";

export default {
    name: 'questionnaireanswer',
    methods: {$gettext},

    props: {
        questionnaireData: Object,
    }
}
</script>
