<template>

    <div :class="{ mandatory: question.questiondata.mandatory == 1}">
        <!-- TODO questionnaire/_answer_description_container as template? -->
        <label>
            <div class="description_container">
                <div class="icon_container">
                    <StudipIcon shape="guestbook"
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
        </label>

        <textarea name=""
                    :required="question.questiondata.mandatory == 1"
                  placeholder="Deine Antwort"
        ></textarea>

    </div>


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



</script>
