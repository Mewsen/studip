<template>
    <form action="#"
          method="post"
          enctype="multipart/form-data"
          class="questionnaire default"
          @submit.prevent="submit()"
          :data-dialog="asDialog ? true : null"
          :data-secure="activateFormSecure"
    >


        <div v-if="questionnaireData.questions[currentPage]">

            <div class="questionnaire_answer">

                Seite {{ currentPage + 1 }} von {{ totalPages }}

                <article
                    v-for="element in questionnaireData.questions[currentPage]"
                    :key="element.id">

                    <QuestionnaireInfoView v-if="element.questiontype == 'QuestionnaireInfo'" :question="element" />
                    <HeadlineView v-if="element.questiontype == 'Headline'" :question="element" />
                    <DividerView v-if="element.questiontype == 'Divider'" />
                    <BlankLineView v-if="element.questiontype == 'BlankLine'" />

                    <VoteAnswer v-if="element.questiontype == 'Vote'" :question="element"/>
                    <FreetextAnswer v-if="element.questiontype == 'Freetext'" :question="element"/>
                    <RangescaleAnswer v-if="element.questiontype == 'Rangescale'" :question="element"/>
                    <LikertAnswer v-if="element.questiontype == 'LikertScale'" :question="element"/>
                    <AutomatedDataAnswer v-if="element.questiontype == 'QuestionnaireAutomatedData'" :question="element"/>
                </article>

            </div>

        </div>

        <!-- Navigation -->
        <div>
            <button :style="{visibility: currentPage > 0 ? 'visible' : 'hidden'}" class="button arr_left" @click="prevPage">
                {{ $gettext('zurück') }}
            </button>

                <button :style="{visibility: currentPage < totalPages - 1 ? 'visible' : 'hidden'}" class="button arr_right" @click="nextPage">
                    {{ $gettext('weiter') }}
                </button>

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
import { computed, ref } from "vue";

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

const currentPage = ref(0)
const totalPages = computed(() => props.questionnaireData.questions.length)

const nextPage = () => {
    if (currentPage.value < totalPages.value - 1) {
        currentPage.value++
    }
}

const prevPage = () => {
    if (currentPage.value > 0) {
        currentPage.value--
    }
}

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
