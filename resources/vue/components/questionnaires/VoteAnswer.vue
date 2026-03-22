<template>

    <div :class="{ mandatory: question.questiondata.mandatory == 1}">
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
        <!-- hidden invalidation notice -->

        <ul class="clean">

            <li v-for="option in shuffledOptions" :key="option.index">
                <!-- name="answers[<?= $vote->getId() ?>][answerdata][answers][<?= $index ?>]" -->
                    <input
                        :type="question.questiondata.multiplechoice == 1 ? 'checkbox' : 'radio'"
                        :name="'answers[' + question.id + '][answerdata][answers][' + option.index + ']'"
                        :value="option.index"
                        v-model="userAnswer"
                        :id="'question-' + question.id + '-' + option.index"
                    />
                <label :for="'question-' + question.id + '-' + option.index" class="question-label">{{ option.answer }}</label>
            </li>

            <template v-if="question.questiondata.freetextfield == 1">
                <li>
                    <input
                        :type="question.questiondata.multiplechoice == 1 ? 'checkbox' : 'radio'"
                        :name="'question-' + question.id"
                        v-model="freeTextEnabled"
                        id="free_answer"
                        :aria-label="$gettext('Geben Sie eine andere Antwort an')"
                        />
                    <label id="free_answer_label" for="free_answer" class="question-label">{{ $gettext('Sonstiges') + ':' }}</label>

                    <div>
                        <textarea
                            v-model="freeTextAnswer"
                            :disabled="!freeTextEnabled"
                            aria-labelledby="free_answer_label"
                        ></textarea>
                    </div>

                </li>
            </template>

        </ul>

    </div>

    <pre>{{ userAnswer }}</pre>
    <pre>{{ freeTextAnswer }}</pre>

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
const userAnswer = ref([])
const freeTextAnswer = ref('')
const freeTextEnabled = ref(false)

function prepareAnswers() {
    const options = props.question.questiondata.options

    // Antworten mit Originalindex kombinieren
    const mapped = options.map((answer, index) => ({
        answer,
        index
    }))

    // Shuffle falls aktiviert
    if (props.question.questiondata.randomize == 1) {
        for (let i = mapped.length - 1; i > 0; i--) {
            const j = Math.floor(Math.random() * (i + 1))
            ;[mapped[i], mapped[j]] = [mapped[j], mapped[i]]
        }
    }

    shuffledOptions.value = mapped
}


function initUserAnswer() {
    const answers = props.question.responseData?.answers ?? []

    if (props.question.questiondata.multiplechoice == 1) {
        userAnswer.value = answers
    } else {
        userAnswer.value = answers[0] ?? null
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

<style scoped lang="scss">


    .question-label {
        display: inline !important;
    }


</style>
