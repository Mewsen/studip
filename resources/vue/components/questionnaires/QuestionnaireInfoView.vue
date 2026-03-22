<template>


    <div class="description_container">
        <div class="icon_container">
            <StudipIcon shape="info-circle"
                        role="info"/>
        </div>
        <div class="description">

            <iframe
                    :src="question.questiondata.url">

            </iframe>

            <article v-html="question.questiondata.description">


            </article>
        </div>
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
