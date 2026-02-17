<!--
$answers = $vote->questiondata['options'];
$indexMap = count($answers) ? range(0, count($answers) - 1) : [];
if ($vote->questiondata['randomize']) {
shuffle($indexMap);
}

$response = $vote->getMyAnswer();
$responseData = $response['answerdata'] ? $response['answerdata']->getArrayCopy() : [];
-->

<template>

    <div :class="{ mandatory: question.questiondata.mandatory == 1}">
        <!-- TODO questionnaire/_answer_description_container as template? -->
        <div class="description_container">
            <div class="icon_container">
                <StudipIcon shape="vote"
                            role="info"/>
            </div>
            <article class="description">
                <StudipIcon v-if="question.questiondata.mandatory == 1"
                            shape="star"
                            role="attention"/>
                <span v-if="question.questiondata.mandatory == 1">{{ $gettext('Pflichtantwort')}}</span>
                <div v-html="question.questiondata.description"></div>
            </article>
        </div>
        <!-- -->
        <!-- hidden invalidation notice -->

        <ul class="clean">

            <li v-for="(answer, index) in shuffledOptions"
                :key="index">
                <label>
                    <input
                        :type="question.questiondata.multiplechoice == 1 ? 'checkbox' : 'radio'"
                        :name="'question-' + question.id"
                        :value="answer"
                        v-model="userAnswer"
                    />

                    {{ answer }}
                </label>

            </li>

        </ul>

    </div>

    <pre>{{ userAnswer }}</pre>

</template>


<script setup>
import { ref, onMounted, watch } from 'vue';
import {$gettext} from "../../../assets/javascripts/lib/gettext";
import StudipIcon from "../StudipIcon.vue";

const props = defineProps({
    question: {
        type: Object,
        required: true
    }
})

const shuffledOptions = ref([])
const userAnswer = ref(null) // Radio
// Für Checkbox später automatisch Array

function prepareAnswers() {
    const options = [...props.question.questiondata.options]

    if (props.question.questiondata.randomize == 1) {
        for (let i = options.length - 1; i > 0; i--) {
            const j = Math.floor(Math.random() * (i + 1))
            ;[options[i], options[j]] = [options[j], options[i]]
        }
    }

    shuffledOptions.value = options
}

function initUserAnswer() {
    if (props.question.questiondata.multiplechoice == 1) {
        // Checkbox → Array
        userAnswer.value = Array.isArray(props.question.responseData)
            ? props.question.responseData
            : []
    } else {
        // Radio → Einzelwert
        userAnswer.value = props.question.responseData || null
    }
}

onMounted(() => {
    prepareAnswers()
    initUserAnswer()
})

watch(
    () => props.question,
    () => {
        prepareAnswers()
        initUserAnswer()
    },
    { deep: true }
)

</script>

