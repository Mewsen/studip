<template>
    <form action="#"
          method="post"
          enctype="multipart/form-data"
          class="questionnaire default"
          @submit.prevent="submit()"
          :data-dialog="asDialog ? true : null"
          :data-secure="activateFormSecure"
    >

        <div class="questionnaire_answer">
            <article v-for="question in questionnaireData.questions">
                {{ question.questiontype }}
                <QuestionnaireInfoView v-if="question.questiontype == 'QuestionnaireInfo'" :question="question" />
                <HeadlineView v-if="question.questiontype == 'Headline'" :question="question" />
                <DividerView v-if="question.questiontype == 'Divider'" />
                <BlankLineView v-if="question.questiontype == 'BlankLine'" />
                <PagebreakView v-if="question.questiontype == 'Pagebreak'" />

                <VoteAnswer v-if="question.questiontype == 'Vote'" :question="question"/>
                <FreetextAnswer v-if="question.questiontype == 'Freetext'" :question="question"/>
                <RangescaleAnswer v-if="question.questiontype == 'Rangescale'" :question="question"/>
                <LikertAnswer v-if="question.questiontype == 'LikertScale'" :question="question"/>
                <AutomatedDataAnswer v-if="question.questiontype == 'QuestionnaireAutomatedData'" :question="question"/>
            </article>

        </div>

        <div class="terms">
            <span v-if="questionnaireData.anonymous == 1 ">{{ $gettext('Die Teilnahme ist anonym.') }}</span>
            <span v-else>{{ $gettext('Die Teilnahme ist nicht anonym.') }}</span>
            <span v-if="questionnaireData.editanswers == 1 ">{{ $gettext('Sie können Ihre Antworten nachträglich ändern.') }}</span>
            <span v-if="questionnaireData.stopdate">{{ $gettext('Sie können den Fragebogen beantworten bis zum %{date} um %{time} Uhr.', {date:getFormattedDate, time:getFormattedTime}) }}</span>
        </div>

    </form>
</template>

<script setup>
import { $gettext } from "../../../assets/javascripts/lib/gettext";
import { computed } from "vue";

/* Import Design Element views */
import QuestionnaireInfoView from '../../components/questionnaires/QuestionnaireInfoView.vue';
import HeadlineView from '../../components/questionnaires/HeadlineView.vue';
import DividerView from '../../components/questionnaires/DividerView.vue';
import BlankLineView from '../../components/questionnaires/BlankLineView.vue';
import PagebreakView from '../../components/questionnaires/PagebreakView.vue';

/* Import Question Views */
import VoteAnswer from '../../components/questionnaires/VoteAnswer.vue';
import FreetextAnswer from '../../components/questionnaires/FreetextAnswer.vue';
import RangescaleAnswer from '../../components/questionnaires/RangescaleAnswer.vue';
import LikertAnswer from '../../components/questionnaires/LikertAnswer.vue';
import AutomatedDataAnswer from '../../components/questionnaires/AutomatedDataAnswer.vue';

const props = defineProps({
    questionnaireData: Object
})

const getFormattedDate = computed(() => {
    if (!props.questionnaireData?.stopdate) return ''

    return new Date(props.questionnaireData.stopdate * 1000)
        .toLocaleDateString(document.documentElement.lang, {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric'
        })
})

const getFormattedTime = computed(() => {
    if (!props.questionnaireData?.stopdate) return ''

    return new Date(props.questionnaireData.stopdate * 1000)
        .toLocaleTimeString(document.documentElement.lang, {
            hour: '2-digit',
            minute: '2-digit'
        })
})
</script>
